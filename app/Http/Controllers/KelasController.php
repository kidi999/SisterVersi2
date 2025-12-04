<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kelas::with(['mataKuliah.programStudi', 'dosen']);

        // Filter by search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_kelas', 'like', "%{$search}%")
                  ->orWhere('nama_kelas', 'like', "%{$search}%")
                  ->orWhereHas('mataKuliah', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by tahun ajaran
        if ($request->has('tahun_ajaran') && $request->tahun_ajaran != '') {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester != '') {
            $query->where('semester', $request->semester);
        }

        $kelas = $query->latest()->paginate(10)->withQueryString();
        
        // Get distinct tahun ajaran for filter
        $tahunAjaranList = Kelas::select('tahun_ajaran')->distinct()->orderBy('tahun_ajaran', 'desc')->pluck('tahun_ajaran');

        return view('kelas.index', compact('kelas', 'tahunAjaranList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mataKuliah = MataKuliah::with('programStudi')->orderBy('nama')->get();
        $dosen = Dosen::orderBy('nama')->get();
        $tahunAkademik = TahunAkademik::where('is_active', true)->get();
        
        return view('kelas.create', compact('mataKuliah', 'dosen', 'tahunAkademik'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'kode_kelas' => 'required|string|max:10|unique:kelas,kode_kelas',
            'nama_kelas' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'kapasitas' => 'required|integer|min:1|max:100',
        ]);

        try {
            DB::beginTransaction();

            $kelas = Kelas::create([
                'mata_kuliah_id' => $validated['mata_kuliah_id'],
                'dosen_id' => $validated['dosen_id'],
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester' => $validated['semester'],
                'kapasitas' => $validated['kapasitas'],
                'terisi' => 0,
                'inserted_by' => Auth::user()->name,
                'inserted_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('kelas.index')
                           ->with('success', 'Kelas berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal menambahkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kela)
    {
        $kela->load(['mataKuliah.programStudi', 'dosen', 'krsItems.mahasiswa']);
        
        return view('kelas.show', compact('kela'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kela)
    {
        $mataKuliah = MataKuliah::with('programStudi')->orderBy('nama')->get();
        $dosen = Dosen::orderBy('nama')->get();
        $tahunAkademik = TahunAkademik::where('is_active', true)->get();
        
        return view('kelas.edit', compact('kela', 'mataKuliah', 'dosen', 'tahunAkademik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kela)
    {
        $validated = $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'dosen_id' => 'required|exists:dosen,id',
            'kode_kelas' => 'required|string|max:10|unique:kelas,kode_kelas,' . $kela->id,
            'nama_kelas' => 'required|string|max:100',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:Ganjil,Genap',
            'kapasitas' => 'required|integer|min:1|max:100',
        ]);

        try {
            DB::beginTransaction();

            // Check if new capacity is less than current filled slots
            if ($validated['kapasitas'] < $kela->terisi) {
                throw new \Exception('Kapasitas tidak boleh kurang dari jumlah mahasiswa yang sudah terdaftar (' . $kela->terisi . ')');
            }

            $kela->update([
                'mata_kuliah_id' => $validated['mata_kuliah_id'],
                'dosen_id' => $validated['dosen_id'],
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tahun_ajaran' => $validated['tahun_ajaran'],
                'semester' => $validated['semester'],
                'kapasitas' => $validated['kapasitas'],
                'updated_by' => Auth::user()->name,
            ]);

            DB::commit();

            return redirect()->route('kelas.index')
                           ->with('success', 'Kelas berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Gagal memperbarui kelas: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Kelas $kela)
    {
        try {
            // Check if kelas has students enrolled
            if ($kela->terisi > 0) {
                return redirect()->back()
                               ->with('error', 'Tidak dapat menghapus kelas yang masih memiliki mahasiswa terdaftar');
            }

            $kela->update([
                'deleted_by' => Auth::user()->name,
            ]);
            
            $kela->delete();

            return redirect()->route('kelas.index')
                           ->with('success', 'Kelas berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }

    /**
     * Display trashed items.
     */
    public function trash(Request $request)
    {
        $query = Kelas::onlyTrashed()->with(['mataKuliah.programStudi', 'dosen']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_kelas', 'like', "%{$search}%")
                  ->orWhere('nama_kelas', 'like', "%{$search}%");
            });
        }

        $kelas = $query->latest('deleted_at')->paginate(10)->withQueryString();

        return view('kelas.trash', compact('kelas'));
    }

    /**
     * Restore trashed item.
     */
    public function restore($id)
    {
        try {
            $kelas = Kelas::onlyTrashed()->findOrFail($id);
            $kelas->restore();

            return redirect()->route('kelas.trash')
                           ->with('success', 'Kelas berhasil dipulihkan');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal memulihkan kelas: ' . $e->getMessage());
        }
    }

    /**
     * Permanently delete trashed item.
     */
    public function forceDelete($id)
    {
        try {
            $kelas = Kelas::onlyTrashed()->findOrFail($id);
            $kelas->forceDelete();

            return redirect()->route('kelas.trash')
                           ->with('success', 'Kelas berhasil dihapus permanen');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Gagal menghapus kelas: ' . $e->getMessage());
        }
    }
}

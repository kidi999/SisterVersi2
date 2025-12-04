<?php

namespace App\Http\Controllers;

use App\Models\AkreditasiProgramStudi;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkreditasiProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AkreditasiProgramStudi::with('programStudi.fakultas', 'files');

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->whereHas('programStudi', function($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id);
            });
        }

        // Filter by program studi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by peringkat
        if ($request->filled('peringkat')) {
            $query->where('peringkat', $request->peringkat);
        }

        // Filter by year
        if ($request->filled('tahun')) {
            $query->where('tahun_akreditasi', $request->tahun);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nomor_sk', 'like', "%{$search}%")
                  ->orWhere('lembaga_akreditasi', 'like', "%{$search}%")
                  ->orWhereHas('programStudi', function($q) use ($search) {
                      $q->where('nama_prodi', 'like', "%{$search}%");
                  });
            });
        }

        $akreditasi = $query->orderBy('tanggal_sk', 'desc')->paginate(10);
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        $prodiList = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();

        return view('akreditasi-program-studi.index', compact('akreditasi', 'fakultasList', 'prodiList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        $prodiList = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        return view('akreditasi-program-studi.create', compact('fakultasList', 'prodiList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasi = AkreditasiProgramStudi::create([
            'program_studi_id' => $request->program_studi_id,
            'lembaga_akreditasi' => $request->lembaga_akreditasi,
            'nomor_sk' => $request->nomor_sk,
            'tanggal_sk' => $request->tanggal_sk,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'peringkat' => $request->peringkat,
            'tahun_akreditasi' => $request->tahun_akreditasi,
            'catatan' => $request->catatan,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        // Link uploaded files
        if ($request->filled('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                FileUpload::whereIn('id', $fileIds)->update([
                    'fileable_id' => $akreditasi->id,
                    'fileable_type' => AkreditasiProgramStudi::class,
                ]);
            }
        }

        return redirect()->route('akreditasi-program-studi.index')
            ->with('success', 'Data akreditasi program studi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AkreditasiProgramStudi $akreditasiProgramStudi)
    {
        $akreditasiProgramStudi->load('programStudi.fakultas', 'files');
        return view('akreditasi-program-studi.show', compact('akreditasiProgramStudi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AkreditasiProgramStudi $akreditasiProgramStudi)
    {
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        $prodiList = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        $akreditasiProgramStudi->load('files');
        return view('akreditasi-program-studi.edit', compact('akreditasiProgramStudi', 'fakultasList', 'prodiList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AkreditasiProgramStudi $akreditasiProgramStudi)
    {
        $request->validate([
            'program_studi_id' => 'required|exists:program_studi,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasiProgramStudi->update([
            'program_studi_id' => $request->program_studi_id,
            'lembaga_akreditasi' => $request->lembaga_akreditasi,
            'nomor_sk' => $request->nomor_sk,
            'tanggal_sk' => $request->tanggal_sk,
            'tanggal_berakhir' => $request->tanggal_berakhir,
            'peringkat' => $request->peringkat,
            'tahun_akreditasi' => $request->tahun_akreditasi,
            'catatan' => $request->catatan,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);

        // Link uploaded files
        if ($request->filled('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                FileUpload::whereIn('id', $fileIds)
                    ->where('fileable_id', 0)
                    ->update([
                        'fileable_id' => $akreditasiProgramStudi->id,
                        'fileable_type' => AkreditasiProgramStudi::class,
                    ]);
            }
        }

        return redirect()->route('akreditasi-program-studi.index')
            ->with('success', 'Data akreditasi program studi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AkreditasiProgramStudi $akreditasiProgramStudi)
    {
        $akreditasiProgramStudi->deleted_by = Auth::id();
        $akreditasiProgramStudi->save();
        $akreditasiProgramStudi->delete();

        return redirect()->route('akreditasi-program-studi.index')
            ->with('success', 'Data akreditasi program studi berhasil dihapus.');
    }

    /**
     * Display trashed records.
     */
    public function trash()
    {
        $akreditasi = AkreditasiProgramStudi::onlyTrashed()
            ->with('programStudi.fakultas')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('akreditasi-program-studi.trash', compact('akreditasi'));
    }

    /**
     * Restore a trashed record.
     */
    public function restore($id)
    {
        $akreditasi = AkreditasiProgramStudi::onlyTrashed()->findOrFail($id);
        $akreditasi->restore();

        return redirect()->route('akreditasi-program-studi.trash')
            ->with('success', 'Data akreditasi program studi berhasil dipulihkan.');
    }

    /**
     * Permanently delete a trashed record.
     */
    public function forceDelete($id)
    {
        $akreditasi = AkreditasiProgramStudi::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($akreditasi->files as $file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }
        
        $akreditasi->forceDelete();

        return redirect()->route('akreditasi-program-studi.trash')
            ->with('success', 'Data akreditasi program studi berhasil dihapus permanen.');
    }
}

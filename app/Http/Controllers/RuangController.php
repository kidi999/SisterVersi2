<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RuangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ruang::with(['fakultas', 'programStudi']);

        // Filter berdasarkan tingkat kepemilikan
        if ($request->filled('tingkat_kepemilikan')) {
            $query->where('tingkat_kepemilikan', $request->tingkat_kepemilikan);
        }

        // Filter berdasarkan jenis ruang
        if ($request->filled('jenis_ruang')) {
            $query->where('jenis_ruang', $request->jenis_ruang);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan fakultas
        if ($request->filled('fakultas_id')) {
            $query->where(function($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                  ->orWhere('tingkat_kepemilikan', 'Universitas');
            });
        }

        // Filter berdasarkan program studi
        if ($request->filled('program_studi_id')) {
            $prodi = ProgramStudi::find($request->program_studi_id);
            if ($prodi) {
                $query->accessibleByProdi($request->program_studi_id, $prodi->fakultas_id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_ruang', 'like', "%{$search}%")
                  ->orWhere('nama_ruang', 'like', "%{$search}%")
                  ->orWhere('gedung', 'like', "%{$search}%");
            });
        }

        $ruang = $query->latest()->paginate(20);
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();

        return view('ruang.index', compact('ruang', 'fakultas', 'programStudi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        
        return view('ruang.create', compact('fakultas', 'programStudi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'kode_ruang' => 'required|string|max:20|unique:ruang',
            'nama_ruang' => 'required|string|max:100',
            'gedung' => 'nullable|string|max:50',
            'lantai' => 'nullable|string|max:10',
            'kapasitas' => 'required|integer|min:0',
            'jenis_ruang' => 'required|in:Kelas,Lab,Perpustakaan,Aula,Ruang Seminar,Ruang Rapat,Lainnya',
            'tingkat_kepemilikan' => 'required|in:Universitas,Fakultas,Prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif,Dalam Perbaikan',
            'keterangan' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            'file_descriptions.*' => 'nullable|string|max:255'
        ];

        // Validasi kepemilikan
        if ($request->tingkat_kepemilikan === 'Fakultas') {
            $rules['fakultas_id'] = 'required|exists:fakultas,id';
        } elseif ($request->tingkat_kepemilikan === 'Prodi') {
            $rules['program_studi_id'] = 'required|exists:program_studi,id';
        }

        $validated = $request->validate($rules);

        // Reset fakultas_id dan program_studi_id jika tingkat kepemilikan adalah Universitas
        if ($validated['tingkat_kepemilikan'] === 'Universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['tingkat_kepemilikan'] === 'Fakultas') {
            $validated['program_studi_id'] = null;
        }

        DB::beginTransaction();
        try {
            $ruang = Ruang::create($validated);

            // Handle file uploads
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $descriptions = $request->input('file_descriptions', []);

                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . $index . '_' . $originalName;
                        $filePath = $file->storeAs('ruang', $fileName, 'public');

                        FileUpload::create([
                            'fileable_type' => Ruang::class,
                            'fileable_id' => $ruang->id,
                            'file_name' => $originalName,
                            'file_path' => $filePath,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                            'description' => $descriptions[$index] ?? null,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('ruang.index')
                ->with('success', 'Data ruang berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruang $ruang)
    {
        $ruang->load(['fakultas', 'programStudi', 'createdBy', 'updatedBy', 'files']);
        
        return view('ruang.show', compact('ruang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruang $ruang)
    {
        $ruang->load('files');
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        
        return view('ruang.edit', compact('ruang', 'fakultas', 'programStudi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruang $ruang)
    {
        $rules = [
            'kode_ruang' => 'required|string|max:20|unique:ruang,kode_ruang,' . $ruang->id,
            'nama_ruang' => 'required|string|max:100',
            'gedung' => 'nullable|string|max:50',
            'lantai' => 'nullable|string|max:10',
            'kapasitas' => 'required|integer|min:0',
            'jenis_ruang' => 'required|in:Kelas,Lab,Perpustakaan,Aula,Ruang Seminar,Ruang Rapat,Lainnya',
            'tingkat_kepemilikan' => 'required|in:Universitas,Fakultas,Prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'fasilitas' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif,Dalam Perbaikan',
            'keterangan' => 'nullable|string',
            'files.*' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            'file_descriptions.*' => 'nullable|string|max:255'
        ];

        // Validasi kepemilikan
        if ($request->tingkat_kepemilikan === 'Fakultas') {
            $rules['fakultas_id'] = 'required|exists:fakultas,id';
        } elseif ($request->tingkat_kepemilikan === 'Prodi') {
            $rules['program_studi_id'] = 'required|exists:program_studi,id';
        }

        $validated = $request->validate($rules);

        // Reset fakultas_id dan program_studi_id jika tingkat kepemilikan adalah Universitas
        if ($validated['tingkat_kepemilikan'] === 'Universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['tingkat_kepemilikan'] === 'Fakultas') {
            $validated['program_studi_id'] = null;
        }

        DB::beginTransaction();
        try {
            $ruang->update($validated);

            // Handle file deletions
            if ($request->has('delete_files')) {
                foreach ($request->delete_files as $fileId) {
                    $file = FileUpload::find($fileId);
                    if ($file && $file->fileable_id == $ruang->id) {
                        Storage::disk('public')->delete($file->file_path);
                        $file->delete();
                    }
                }
            }

            // Handle new file uploads
            if ($request->hasFile('files')) {
                $files = $request->file('files');
                $descriptions = $request->input('file_descriptions', []);

                foreach ($files as $index => $file) {
                    if ($file && $file->isValid()) {
                        $originalName = $file->getClientOriginalName();
                        $fileName = time() . '_' . $index . '_' . $originalName;
                        $filePath = $file->storeAs('ruang', $fileName, 'public');

                        FileUpload::create([
                            'fileable_type' => Ruang::class,
                            'fileable_id' => $ruang->id,
                            'file_name' => $originalName,
                            'file_path' => $filePath,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                            'description' => $descriptions[$index] ?? null,
                            'created_by' => auth()->id()
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('ruang.index')
                ->with('success', 'Data ruang berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruang $ruang)
    {
        $ruang->delete();

        return redirect()->route('ruang.index')
            ->with('success', 'Data ruang berhasil dihapus');
    }

    /**
     * Trash - soft deleted items
     */
    public function trash()
    {
        $ruang = Ruang::onlyTrashed()
            ->with(['fakultas', 'programStudi'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('ruang.trash', compact('ruang'));
    }

    /**
     * Restore dari trash
     */
    public function restore($id)
    {
        $ruang = Ruang::onlyTrashed()->findOrFail($id);
        $ruang->restore();

        return redirect()->route('ruang.trash')
            ->with('success', 'Data ruang berhasil dipulihkan');
    }

    /**
     * Force delete permanent
     */
    public function forceDelete($id)
    {
        $ruang = Ruang::onlyTrashed()->findOrFail($id);
        $ruang->forceDelete();

        return redirect()->route('ruang.trash')
            ->with('success', 'Data ruang berhasil dihapus permanen');
    }

    /**
     * Get program studi by fakultas (API)
     */
    public function getProdiByFakultas($fakultasId)
    {
        $programStudi = ProgramStudi::where('fakultas_id', $fakultasId)
            ->orderBy('nama_prodi')
            ->get(['id', 'nama_prodi', 'jenjang']);

        return response()->json($programStudi);
    }
}

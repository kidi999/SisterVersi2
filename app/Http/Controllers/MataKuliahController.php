<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::with(['fakultas', 'programStudi.fakultas']);

        // Smart filtering based on level
        if ($request->filled('level_matkul')) {
            $level = $request->level_matkul;
            
            if ($level === 'universitas') {
                $query->where('level_matkul', 'universitas');
            } elseif ($level === 'fakultas') {
                $query->where(function($q) {
                    $q->where('level_matkul', 'fakultas')
                      ->orWhere('level_matkul', 'universitas'); // Universitas matkul juga muncul
                });
            } elseif ($level === 'prodi') {
                $query->where(function($q) {
                    $q->where('level_matkul', 'prodi')
                      ->orWhere('level_matkul', 'fakultas')
                      ->orWhere('level_matkul', 'universitas'); // Semua level muncul
                });
            }
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->where(function($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                  ->orWhere('level_matkul', 'universitas') // Universitas matkul muncul di semua fakultas
                  ->orWhereHas('programStudi', function($q2) use ($request) {
                      $q2->where('fakultas_id', $request->fakultas_id);
                  });
            });
        }

        // Filter by program studi
        if ($request->filled('program_studi_id')) {
            $query->where(function($q) use ($request) {
                $q->where('program_studi_id', $request->program_studi_id)
                  ->orWhere('level_matkul', 'universitas') // Universitas matkul muncul di semua prodi
                  ->orWhere(function($q2) use ($request) {
                      $q2->where('level_matkul', 'fakultas')
                         ->whereHas('fakultas', function($q3) use ($request) {
                             $prodi = ProgramStudi::find($request->program_studi_id);
                             if ($prodi) {
                                 $q3->where('id', $prodi->fakultas_id);
                             }
                         });
                  });
            });
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_mk', 'like', "%{$search}%")
                  ->orWhere('nama_mk', 'like', "%{$search}%");
            });
        }

        $mataKuliah = $query->latest()->paginate(20);
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();

        return view('mata-kuliah.index', compact('mataKuliah', 'fakultas', 'programStudi'));
    }

    public function create()
    {
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();
        return view('mata-kuliah.create', compact('fakultas', 'programStudi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_matkul' => 'required|in:universitas,fakultas,prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'kode_mk' => 'required|unique:mata_kuliah|max:20',
            'nama_mk' => 'required|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
            'deskripsi' => 'nullable',
            'file_ids' => 'sometimes|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        // Validasi khusus berdasarkan level
        if ($validated['level_matkul'] === 'fakultas' && empty($validated['fakultas_id'])) {
            return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Mata Kuliah Fakultas'])->withInput();
        }
        if ($validated['level_matkul'] === 'prodi') {
            if (empty($validated['fakultas_id'])) {
                return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Mata Kuliah Program Studi'])->withInput();
            }
            if (empty($validated['program_studi_id'])) {
                return redirect()->back()->withErrors(['program_studi_id' => 'Program Studi wajib diisi untuk Mata Kuliah Program Studi'])->withInput();
            }
        }

        // Set null untuk field yang tidak digunakan berdasarkan level
        if ($validated['level_matkul'] === 'universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['level_matkul'] === 'fakultas') {
            $validated['program_studi_id'] = null;
        }

        $validated['created_by'] = Auth::id();
        $mataKuliah = MataKuliah::create($validated);

        // Attach files if any
        if ($request->has('file_ids') && is_array($request->file_ids)) {
            FileUpload::whereIn('id', $request->file_ids)
                ->update([
                    'fileable_id' => $mataKuliah->id,
                    'fileable_type' => MataKuliah::class
                ]);
        }

        return redirect()->route('mata-kuliah.index')->with('success', 'Data mata kuliah berhasil ditambahkan');
    }

    public function show(MataKuliah $mataKuliah)
    {
        $mataKuliah->load(['fakultas', 'programStudi.fakultas', 'files']);
        return view('mata-kuliah.show', compact('mataKuliah'));
    }

    public function edit(MataKuliah $mataKuliah)
    {
        $mataKuliah->load('files');
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();
        return view('mata-kuliah.edit', compact('mataKuliah', 'fakultas', 'programStudi'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $validated = $request->validate([
            'level_matkul' => 'required|in:universitas,fakultas,prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'kode_mk' => 'required|max:20|unique:mata_kuliah,kode_mk,' . $mataKuliah->id,
            'nama_mk' => 'required|max:100',
            'sks' => 'required|integer|min:1|max:6',
            'semester' => 'required|integer|min:1|max:14',
            'jenis' => 'required|in:Wajib,Pilihan',
            'deskripsi' => 'nullable',
            'file_ids' => 'sometimes|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        // Validasi khusus berdasarkan level
        if ($validated['level_matkul'] === 'fakultas' && empty($validated['fakultas_id'])) {
            return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Mata Kuliah Fakultas'])->withInput();
        }
        if ($validated['level_matkul'] === 'prodi') {
            if (empty($validated['fakultas_id'])) {
                return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Mata Kuliah Program Studi'])->withInput();
            }
            if (empty($validated['program_studi_id'])) {
                return redirect()->back()->withErrors(['program_studi_id' => 'Program Studi wajib diisi untuk Mata Kuliah Program Studi'])->withInput();
            }
        }

        // Set null untuk field yang tidak digunakan berdasarkan level
        if ($validated['level_matkul'] === 'universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['level_matkul'] === 'fakultas') {
            $validated['program_studi_id'] = null;
        }

        $validated['updated_by'] = Auth::id();
        $mataKuliah->update($validated);

        // Update files if any
        if ($request->has('file_ids') && is_array($request->file_ids)) {
            // Get current file IDs
            $currentFileIds = FileUpload::where('fileable_type', MataKuliah::class)
                ->where('fileable_id', $mataKuliah->id)
                ->pluck('id')
                ->toArray();
            
            // Find files to delete
            $filesToDelete = array_diff($currentFileIds, $request->file_ids);
            if (!empty($filesToDelete)) {
                FileUpload::whereIn('id', $filesToDelete)->delete();
            }
            
            // Find files to attach
            $filesToAttach = array_diff($request->file_ids, $currentFileIds);
            if (!empty($filesToAttach)) {
                FileUpload::whereIn('id', $filesToAttach)
                    ->update([
                        'fileable_id' => $mataKuliah->id,
                        'fileable_type' => MataKuliah::class
                    ]);
            }
        } else {
            // If no files in request, delete all existing files
            FileUpload::where('fileable_type', MataKuliah::class)
                ->where('fileable_id', $mataKuliah->id)
                ->delete();
        }

        return redirect()->route('mata-kuliah.index')->with('success', 'Data mata kuliah berhasil diperbarui');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $mataKuliah->deleted_by = Auth::id();
        $mataKuliah->save();
        $mataKuliah->delete();

        return redirect()->route('mata-kuliah.index')->with('success', 'Data mata kuliah berhasil dihapus');
    }

    public function trash()
    {
        $mataKuliah = MataKuliah::onlyTrashed()
            ->with(['fakultas', 'programStudi.fakultas'])
            ->latest('deleted_at')
            ->paginate(20);
        
        return view('mata-kuliah.trash', compact('mataKuliah'));
    }

    public function restore($id)
    {
        $mataKuliah = MataKuliah::onlyTrashed()->findOrFail($id);
        $mataKuliah->restore();

        return redirect()->route('mata-kuliah.trash')->with('success', 'Data mata kuliah berhasil dipulihkan');
    }

    public function forceDelete($id)
    {
        $mataKuliah = MataKuliah::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        FileUpload::where('fileable_type', MataKuliah::class)
            ->where('fileable_id', $id)
            ->delete();
        
        $mataKuliah->forceDelete();

        return redirect()->route('mata-kuliah.trash')->with('success', 'Data mata kuliah berhasil dihapus permanen');
    }
}

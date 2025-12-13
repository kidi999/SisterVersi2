<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with(['fakultas', 'programStudi.fakultas', 'files']);

        // Filter by level
        if ($request->filled('level_dosen')) {
            $query->where('level_dosen', $request->level_dosen);
        }

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->where(function($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                  ->orWhere('level_dosen', 'universitas')
                  ->orWhereHas('programStudi', function($q2) use ($request) {
                      $q2->where('fakultas_id', $request->fakultas_id);
                  });
            });
        }

        // Filter by program studi
        if ($request->filled('program_studi_id')) {
            $query->where(function($q) use ($request) {
                $prodi = ProgramStudi::find($request->program_studi_id);
                $q->where('program_studi_id', $request->program_studi_id)
                  ->orWhere('level_dosen', 'universitas');
                if ($prodi) {
                    $q->orWhere(function($q2) use ($prodi) {
                        $q2->where('level_dosen', 'fakultas')
                           ->where('fakultas_id', $prodi->fakultas_id);
                    });
                }
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by jabatan akademik
        if ($request->filled('jabatan_akademik')) {
            $query->where('jabatan_akademik', $request->jabatan_akademik);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nidn', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $dosen = $query->latest()->paginate(20)->withQueryString();
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();

        return view('dosen.index', compact('dosen', 'fakultas', 'programStudi'));
    }

    public function exportExcel(Request $request)
    {
        $query = Dosen::with(['fakultas', 'programStudi.fakultas']);

        if ($request->filled('level_dosen')) {
            $query->where('level_dosen', $request->level_dosen);
        }

        if ($request->filled('fakultas_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                    ->orWhere('level_dosen', 'universitas')
                    ->orWhereHas('programStudi', function ($q2) use ($request) {
                        $q2->where('fakultas_id', $request->fakultas_id);
                    });
            });
        }

        if ($request->filled('program_studi_id')) {
            $query->where(function ($q) use ($request) {
                $prodi = ProgramStudi::find($request->program_studi_id);
                $q->where('program_studi_id', $request->program_studi_id)
                    ->orWhere('level_dosen', 'universitas');
                if ($prodi) {
                    $q->orWhere(function ($q2) use ($prodi) {
                        $q2->where('level_dosen', 'fakultas')
                            ->where('fakultas_id', $prodi->fakultas_id);
                    });
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jabatan_akademik')) {
            $query->where('jabatan_akademik', $request->jabatan_akademik);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->get();

        $rows = $items->map(function (Dosen $item, int $index) {
            return [
                $index + 1,
                $item->nip,
                $item->nidn ?? '-',
                $item->nama_dosen,
                ucfirst($item->level_dosen ?? '-'),
                $item->scope_label,
                $item->email,
                $item->status,
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'NIP', 'NIDN', 'Nama Dosen', 'Level', 'Scope', 'Email', 'Status'],
            $rows
        );

        return TabularExport::excelResponse('dosen.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $query = Dosen::with(['fakultas', 'programStudi.fakultas']);

        if ($request->filled('level_dosen')) {
            $query->where('level_dosen', $request->level_dosen);
        }

        if ($request->filled('fakultas_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('fakultas_id', $request->fakultas_id)
                    ->orWhere('level_dosen', 'universitas')
                    ->orWhereHas('programStudi', function ($q2) use ($request) {
                        $q2->where('fakultas_id', $request->fakultas_id);
                    });
            });
        }

        if ($request->filled('program_studi_id')) {
            $query->where(function ($q) use ($request) {
                $prodi = ProgramStudi::find($request->program_studi_id);
                $q->where('program_studi_id', $request->program_studi_id)
                    ->orWhere('level_dosen', 'universitas');
                if ($prodi) {
                    $q->orWhere(function ($q2) use ($prodi) {
                        $q2->where('level_dosen', 'fakultas')
                            ->where('fakultas_id', $prodi->fakultas_id);
                    });
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jabatan_akademik')) {
            $query->where('jabatan_akademik', $request->jabatan_akademik);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_dosen', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nidn', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->get();

        $rows = $items->map(function (Dosen $item, int $index) {
            return [
                $index + 1,
                $item->nip,
                $item->nidn ?? '-',
                $item->nama_dosen,
                ucfirst($item->level_dosen ?? '-'),
                $item->scope_label,
                $item->email,
                $item->status,
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'NIP', 'NIDN', 'Nama Dosen', 'Level', 'Scope', 'Email', 'Status'],
            $rows
        );

        return Pdf::loadHTML($html)->download('dosen.pdf');
    }

    public function create()
    {
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();
        $provinces = \App\Models\Province::all();
        return view('dosen.create', compact('fakultas', 'programStudi', 'provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level_dosen' => 'required|in:universitas,fakultas,prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'nip' => 'required|unique:dosen|max:20',
            'nidn' => 'nullable|unique:dosen|max:20',
            'nama_dosen' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'village_id' => 'nullable|exists:villages,id',
            'telepon' => 'nullable|max:20',
            'email' => 'required|email|unique:dosen|max:100',
            'pendidikan_terakhir' => 'nullable|max:10',
            'jabatan_akademik' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Non-Aktif,Cuti',
            'file_ids' => 'sometimes|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        // Validasi khusus berdasarkan level
        if ($validated['level_dosen'] === 'fakultas' && empty($validated['fakultas_id'])) {
            return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Dosen Fakultas'])->withInput();
        }
        if ($validated['level_dosen'] === 'prodi') {
            if (empty($validated['fakultas_id'])) {
                return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Dosen Program Studi'])->withInput();
            }
            if (empty($validated['program_studi_id'])) {
                return redirect()->back()->withErrors(['program_studi_id' => 'Program Studi wajib diisi untuk Dosen Program Studi'])->withInput();
            }
        }

        // Set null untuk field yang tidak digunakan berdasarkan level
        if ($validated['level_dosen'] === 'universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['level_dosen'] === 'fakultas') {
            $validated['program_studi_id'] = null;
        }

        $validated['created_by'] = Auth::id();
        $dosen = Dosen::create($validated);

        // Attach files if any
        if ($request->has('file_ids') && is_array($request->file_ids)) {
            FileUpload::whereIn('id', $request->file_ids)
                ->update([
                    'fileable_id' => $dosen->id,
                    'fileable_type' => Dosen::class
                ]);
        }

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil ditambahkan');
    }

    public function show(Dosen $dosen)
    {
        $dosen->load(['fakultas', 'programStudi.fakultas', 'files']);
        return view('dosen.show', compact('dosen'));
    }

    public function edit(Dosen $dosen)
    {
        $dosen->load('files', 'village.subRegency.regency.province');
        $fakultas = Fakultas::all();
        $programStudi = ProgramStudi::all();
        $provinces = \App\Models\Province::all();
        return view('dosen.edit', compact('dosen', 'fakultas', 'programStudi', 'provinces'));
    }

    public function update(Request $request, Dosen $dosen)
    {
        $validated = $request->validate([
            'level_dosen' => 'required|in:universitas,fakultas,prodi',
            'fakultas_id' => 'nullable|exists:fakultas,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'nip' => 'required|max:20|unique:dosen,nip,' . $dosen->id,
            'nidn' => 'nullable|max:20|unique:dosen,nidn,' . $dosen->id,
            'nama_dosen' => 'required|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|max:50',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable',
            'village_id' => 'nullable|exists:villages,id',
            'telepon' => 'nullable|max:20',
            'email' => 'required|email|max:100|unique:dosen,email,' . $dosen->id,
            'pendidikan_terakhir' => 'nullable|max:10',
            'jabatan_akademik' => 'nullable|max:50',
            'status' => 'required|in:Aktif,Non-Aktif,Cuti',
            'file_ids' => 'sometimes|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        // Validasi khusus berdasarkan level
        if ($validated['level_dosen'] === 'fakultas' && empty($validated['fakultas_id'])) {
            return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Dosen Fakultas'])->withInput();
        }
        if ($validated['level_dosen'] === 'prodi') {
            if (empty($validated['fakultas_id'])) {
                return redirect()->back()->withErrors(['fakultas_id' => 'Fakultas wajib diisi untuk Dosen Program Studi'])->withInput();
            }
            if (empty($validated['program_studi_id'])) {
                return redirect()->back()->withErrors(['program_studi_id' => 'Program Studi wajib diisi untuk Dosen Program Studi'])->withInput();
            }
        }

        // Set null untuk field yang tidak digunakan berdasarkan level
        if ($validated['level_dosen'] === 'universitas') {
            $validated['fakultas_id'] = null;
            $validated['program_studi_id'] = null;
        } elseif ($validated['level_dosen'] === 'fakultas') {
            $validated['program_studi_id'] = null;
        }

        $validated['updated_by'] = Auth::id();
        $dosen->update($validated);

        // Update files if any
        if ($request->has('file_ids') && is_array($request->file_ids)) {
            // Get current file IDs
            $currentFileIds = FileUpload::where('fileable_type', Dosen::class)
                ->where('fileable_id', $dosen->id)
                ->pluck('id')
                ->toArray();
            
            // Find files to delete (files that are currently attached but not in new list)
            $filesToDelete = array_diff($currentFileIds, $request->file_ids);
            if (!empty($filesToDelete)) {
                FileUpload::whereIn('id', $filesToDelete)->delete();
            }
            
            // Find files to attach (files in new list that are not currently attached)
            $filesToAttach = array_diff($request->file_ids, $currentFileIds);
            if (!empty($filesToAttach)) {
                FileUpload::whereIn('id', $filesToAttach)
                    ->update([
                        'fileable_id' => $dosen->id,
                        'fileable_type' => Dosen::class
                    ]);
            }
        } else {
            // If no files in request, delete all existing files
            FileUpload::where('fileable_type', Dosen::class)
                ->where('fileable_id', $dosen->id)
                ->delete();
        }

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diperbarui');
    }

    public function destroy(Dosen $dosen)
    {
        $dosen->deleted_by = Auth::id();
        $dosen->save();
        $dosen->delete();

        return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil dihapus');
    }

    public function trash()
    {
        $dosen = Dosen::onlyTrashed()
            ->with(['fakultas', 'programStudi.fakultas'])
            ->latest('deleted_at')
            ->paginate(20);

        return view('dosen.trash', compact('dosen'));
    }

    public function restore($id)
    {
        $dosen = Dosen::onlyTrashed()->findOrFail($id);
        $dosen->restore();

        return redirect()->route('dosen.trash')->with('success', 'Data dosen berhasil dipulihkan');
    }

    public function forceDelete($id)
    {
        $dosen = Dosen::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($dosen->files as $file) {
            \Storage::delete('public/' . $file->file_path);
            $file->delete();
        }
        
        $dosen->forceDelete();

        return redirect()->route('dosen.trash')->with('success', 'Data dosen berhasil dihapus permanen');
    }
}

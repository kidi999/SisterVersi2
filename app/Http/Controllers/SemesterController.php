<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\ProgramStudi;
use App\Support\TabularExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class SemesterController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::with(['tahunAkademik', 'programStudi.fakultas']);

        // Filter by tahun akademik
        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        // Filter by program studi
        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        // Filter universitas (tanpa prodi)
        if ($request->filled('type') && $request->type == 'universitas') {
            $query->whereNull('program_studi_id');
        }

        $semesters = $query->orderBy('tahun_akademik_id', 'desc')
            ->orderBy('nomor_semester', 'desc')
            ->paginate(25)
            ->withQueryString();

        $tahunAkademiks = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $programStudis = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();

        return view('semester.index', compact('semesters', 'tahunAkademiks', 'programStudis'));
    }

    public function exportExcel(Request $request)
    {
        $query = Semester::with(['tahunAkademik', 'programStudi.fakultas']);

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->filled('type') && $request->type == 'universitas') {
            $query->whereNull('program_studi_id');
        }

        $items = $query->orderBy('tahun_akademik_id', 'desc')
            ->orderBy('nomor_semester', 'desc')
            ->get();

        $rows = $items->map(function (Semester $semester, int $index) {
            $prodi = $semester->program_studi_id ? ($semester->programStudi->nama_prodi ?? '-') : 'UNIVERSITAS';
            $fakultas = $semester->program_studi_id && $semester->programStudi && $semester->programStudi->fakultas
                ? ($semester->programStudi->fakultas->nama_fakultas ?? '-')
                : '-';

            $periode = '';
            if ($semester->tanggal_mulai && $semester->tanggal_selesai) {
                $periode = $semester->tanggal_mulai . ' - ' . $semester->tanggal_selesai;
            }

            return [
                $index + 1,
                $semester->tahunAkademik?->kode ?? '-',
                $prodi,
                $fakultas,
                $semester->nama_semester,
                (string) $semester->nomor_semester,
                $periode,
                $semester->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Tahun Akademik', 'Program Studi', 'Fakultas', 'Nama Semester', 'Nomor', 'Periode', 'Status'],
            $rows
        );

        return TabularExport::excelResponse('semester.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $query = Semester::with(['tahunAkademik', 'programStudi.fakultas']);

        if ($request->filled('tahun_akademik_id')) {
            $query->where('tahun_akademik_id', $request->tahun_akademik_id);
        }

        if ($request->filled('program_studi_id')) {
            $query->where('program_studi_id', $request->program_studi_id);
        }

        if ($request->filled('type') && $request->type == 'universitas') {
            $query->whereNull('program_studi_id');
        }

        $items = $query->orderBy('tahun_akademik_id', 'desc')
            ->orderBy('nomor_semester', 'desc')
            ->get();

        $rows = $items->map(function (Semester $semester, int $index) {
            $prodi = $semester->program_studi_id ? ($semester->programStudi->nama_prodi ?? '-') : 'UNIVERSITAS';
            $fakultas = $semester->program_studi_id && $semester->programStudi && $semester->programStudi->fakultas
                ? ($semester->programStudi->fakultas->nama_fakultas ?? '-')
                : '-';

            $periode = '';
            if ($semester->tanggal_mulai && $semester->tanggal_selesai) {
                $periode = $semester->tanggal_mulai . ' - ' . $semester->tanggal_selesai;
            }

            return [
                $index + 1,
                $semester->tahunAkademik?->kode ?? '-',
                $prodi,
                $fakultas,
                $semester->nama_semester,
                (string) $semester->nomor_semester,
                $periode,
                $semester->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Tahun Akademik', 'Program Studi', 'Fakultas', 'Nama Semester', 'Nomor', 'Periode', 'Status'],
            $rows
        );

        return Pdf::loadHTML($html)->download('semester.pdf');
    }

    public function create(Request $request)
    {
        $tahunAkademiks = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $programStudis = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        
        $tahunAkademikId = $request->get('tahun_akademik');
        
        return view('semester.create', compact('tahunAkademiks', 'programStudis', 'tahunAkademikId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'nama_semester' => 'required|string|max:100',
            'nomor_semester' => 'required|integer|min:1|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_mulai_perkuliahan' => 'nullable|date',
            'tanggal_selesai_perkuliahan' => 'nullable|date|after:tanggal_mulai_perkuliahan',
            'tanggal_mulai_uts' => 'nullable|date',
            'tanggal_selesai_uts' => 'nullable|date|after:tanggal_mulai_uts',
            'tanggal_mulai_uas' => 'nullable|date',
            'tanggal_selesai_uas' => 'nullable|date|after:tanggal_mulai_uas',
            'keterangan' => 'nullable|string',
        ], [
            'tahun_akademik_id.required' => 'Tahun akademik harus dipilih',
            'tahun_akademik_id.exists' => 'Tahun akademik tidak valid',
            'program_studi_id.exists' => 'Program studi tidak valid',
            'nama_semester.required' => 'Nama semester harus diisi',
            'nomor_semester.required' => 'Nomor semester harus diisi',
            'nomor_semester.min' => 'Nomor semester minimal 1',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        $validated['created_by'] = Auth::user()->name;
        $validated['is_active'] = false;

        $semester = Semester::create($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0);
                    })
                    ->update([
                        'fileable_id' => $semester->id,
                        'fileable_type' => 'App\\Models\\Semester'
                    ]);
            }
        }

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function show(Semester $semester)
    {
        $semester->load(['tahunAkademik', 'programStudi.fakultas', 'files']);
        return view('semester.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        $semester->load('files');
        $tahunAkademiks = TahunAkademik::orderBy('tahun_mulai', 'desc')->get();
        $programStudis = ProgramStudi::with('fakultas')->orderBy('nama_prodi')->get();
        
        return view('semester.edit', compact('semester', 'tahunAkademiks', 'programStudis'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'tahun_akademik_id' => 'required|exists:tahun_akademiks,id',
            'program_studi_id' => 'nullable|exists:program_studi,id',
            'nama_semester' => 'required|string|max:100',
            'nomor_semester' => 'required|integer|min:1|max:20',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'tanggal_mulai_perkuliahan' => 'nullable|date',
            'tanggal_selesai_perkuliahan' => 'nullable|date|after:tanggal_mulai_perkuliahan',
            'tanggal_mulai_uts' => 'nullable|date',
            'tanggal_selesai_uts' => 'nullable|date|after:tanggal_mulai_uts',
            'tanggal_mulai_uas' => 'nullable|date',
            'tanggal_selesai_uas' => 'nullable|date|after:tanggal_mulai_uas',
            'keterangan' => 'nullable|string',
        ], [
            'tahun_akademik_id.required' => 'Tahun akademik harus dipilih',
            'tahun_akademik_id.exists' => 'Tahun akademik tidak valid',
            'program_studi_id.exists' => 'Program studi tidak valid',
            'nama_semester.required' => 'Nama semester harus diisi',
            'nomor_semester.required' => 'Nomor semester harus diisi',
            'nomor_semester.min' => 'Nomor semester minimal 1',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $semester->update($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0);
                    })
                    ->update([
                        'fileable_id' => $semester->id,
                        'fileable_type' => 'App\\Models\\Semester'
                    ]);
            }
        }

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil diupdate.');
    }

    public function destroy(Semester $semester)
    {
        $semester->deleted_by = Auth::user()->name;
        $semester->save();
        $semester->delete();

        return redirect()->route('semester.index')
            ->with('success', 'Semester berhasil dihapus.');
    }

    public function toggleActive(Semester $semester)
    {
        // Only one semester can be active per tahun akademik and program studi
        if (!$semester->is_active) {
            Semester::where('tahun_akademik_id', $semester->tahun_akademik_id)
                ->where('program_studi_id', $semester->program_studi_id)
                ->update(['is_active' => false]);
        }

        $semester->is_active = !$semester->is_active;
        $semester->updated_by = Auth::user()->name;
        $semester->save();

        $status = $semester->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return response()->json([
            'success' => true,
            'message' => "Semester berhasil {$status}"
        ]);
    }

    public function trash()
    {
        $this->authorize('super_admin');
        
        $semesters = Semester::onlyTrashed()
            ->with(['tahunAkademik', 'programStudi.fakultas'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('semester.trash', compact('semesters'));
    }

    public function restore($id)
    {
        $this->authorize('super_admin');
        
        $semester = Semester::onlyTrashed()->findOrFail($id);
        $semester->restore();

        return redirect()->route('semester.trash')
            ->with('success', 'Semester berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $this->authorize('super_admin');
        
        $semester = Semester::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($semester->files as $file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }
        
        $semester->forceDelete();

        return redirect()->route('semester.trash')
            ->with('success', 'Semester berhasil dihapus permanen.');
    }
}

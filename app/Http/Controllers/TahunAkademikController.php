<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use App\Support\TabularExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TahunAkademikController extends Controller
{
    public function index()
    {
        $tahunAkademiks = TahunAkademik::withCount('semesters')
            ->orderBy('tahun_mulai', 'desc')
            ->paginate(25)
            ->withQueryString();
        return view('tahun-akademik.index', compact('tahunAkademiks'));
    }

    public function exportExcel(Request $request)
    {
        $items = TahunAkademik::withCount('semesters')
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        $rows = $items->map(function (TahunAkademik $ta, int $index) {
            return [
                $index + 1,
                $ta->kode,
                $ta->nama,
                $ta->tahun_mulai . '/' . $ta->tahun_selesai,
                (string) $ta->semesters_count,
                $ta->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Kode', 'Nama', 'Periode', 'Semester', 'Status'],
            $rows
        );

        return TabularExport::excelResponse('tahun_akademik.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $items = TahunAkademik::withCount('semesters')
            ->orderBy('tahun_mulai', 'desc')
            ->get();

        $rows = $items->map(function (TahunAkademik $ta, int $index) {
            return [
                $index + 1,
                $ta->kode,
                $ta->nama,
                $ta->tahun_mulai . '/' . $ta->tahun_selesai,
                (string) $ta->semesters_count,
                $ta->is_active ? 'Aktif' : 'Nonaktif',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Kode', 'Nama', 'Periode', 'Semester', 'Status'],
            $rows
        );

        return Pdf::loadHTML($html)->download('tahun_akademik.pdf');
    }

    public function create()
    {
        return view('tahun-akademik.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademiks,kode',
            'nama' => 'required|string|max:100',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama tahun akademik harus diisi',
            'tahun_mulai.required' => 'Tahun mulai harus diisi',
            'tahun_selesai.required' => 'Tahun selesai harus diisi',
            'tahun_selesai.gt' => 'Tahun selesai harus lebih besar dari tahun mulai',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        $validated['created_by'] = Auth::user()->name;
        $validated['is_active'] = false;

        $tahunAkademik = TahunAkademik::create($validated);

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
                        'fileable_id' => $tahunAkademik->id,
                        'fileable_type' => 'App\\Models\\TahunAkademik'
                    ]);
            }
        }

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil ditambahkan.');
    }

    public function show(TahunAkademik $tahunAkademik)
    {
        $tahunAkademik->load(['semesters.programStudi.fakultas', 'files']);
        return view('tahun-akademik.show', compact('tahunAkademik'));
    }

    public function edit(TahunAkademik $tahunAkademik)
    {
        $tahunAkademik->load('files');
        return view('tahun-akademik.edit', compact('tahunAkademik'));
    }

    public function update(Request $request, TahunAkademik $tahunAkademik)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:20|unique:tahun_akademiks,kode,' . $tahunAkademik->id,
            'nama' => 'required|string|max:100',
            'tahun_mulai' => 'required|integer|min:2000|max:2100',
            'tahun_selesai' => 'required|integer|min:2000|max:2100|gt:tahun_mulai',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama tahun akademik harus diisi',
            'tahun_mulai.required' => 'Tahun mulai harus diisi',
            'tahun_selesai.required' => 'Tahun selesai harus diisi',
            'tahun_selesai.gt' => 'Tahun selesai harus lebih besar dari tahun mulai',
            'tanggal_mulai.required' => 'Tanggal mulai harus diisi',
            'tanggal_selesai.required' => 'Tanggal selesai harus diisi',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $tahunAkademik->update($validated);

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
                        'fileable_id' => $tahunAkademik->id,
                        'fileable_type' => 'App\\Models\\TahunAkademik'
                    ]);
            }
        }

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil diupdate.');
    }

    public function destroy(TahunAkademik $tahunAkademik)
    {
        $tahunAkademik->deleted_by = Auth::user()->name;
        $tahunAkademik->save();
        $tahunAkademik->delete();

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil dihapus.');
    }

    public function toggleActive(TahunAkademik $tahunAkademik)
    {
        // Jika akan mengaktifkan, nonaktifkan yang lain dulu
        if (!$tahunAkademik->is_active) {
            TahunAkademik::where('is_active', true)->update(['is_active' => false]);
        }

        $tahunAkademik->is_active = !$tahunAkademik->is_active;
        $tahunAkademik->updated_by = Auth::user()->name;
        $tahunAkademik->save();

        return response()->json([
            'success' => true,
            'is_active' => $tahunAkademik->is_active,
            'message' => $tahunAkademik->is_active ? 'Tahun akademik diaktifkan.' : 'Tahun akademik dinonaktifkan.'
        ]);
    }

    public function trash()
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $tahunAkademiks = TahunAkademik::onlyTrashed()->get();
        return view('tahun-akademik.trash', compact('tahunAkademiks'));
    }

    public function restore(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $tahunAkademik = TahunAkademik::onlyTrashed()->findOrFail($id);
        $tahunAkademik->restore();

        return redirect()->route('tahun-akademik.trash')
            ->with('success', 'Tahun akademik berhasil dipulihkan.');
    }

    public function forceDelete(string $id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $tahunAkademik = TahunAkademik::onlyTrashed()->findOrFail($id);
        $tahunAkademik->forceDelete();

        return redirect()->route('tahun-akademik.trash')
            ->with('success', 'Tahun akademik berhasil dihapus permanen.');
    }
}

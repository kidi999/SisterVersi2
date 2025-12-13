<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ProgramStudi;
use App\Models\Fakultas;
use App\Models\FileUpload;
use App\Support\TabularExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ProgramStudi::with('fakultas');

        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('akreditasi')) {
            $query->where('akreditasi', $request->akreditasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_prodi', 'like', "%{$search}%")
                  ->orWhere('kode_prodi', 'like', "%{$search}%")
                  ->orWhere('kaprodi', 'like', "%{$search}%");
            });
        }

        $programStudi = $query->orderBy('nama_prodi')->paginate(10)->withQueryString();
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();

        return view('program-studi.index', compact('programStudi', 'fakultas'));
    }

    public function exportExcel(Request $request)
    {
        $query = ProgramStudi::with('fakultas');

        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('akreditasi')) {
            $query->where('akreditasi', $request->akreditasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_prodi', 'like', "%{$search}%")
                    ->orWhere('kode_prodi', 'like', "%{$search}%")
                    ->orWhere('kaprodi', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama_prodi')->get();

        $rows = $items->map(function (ProgramStudi $prodi, int $index) {
            return [
                $index + 1,
                $prodi->kode_prodi,
                $prodi->nama_prodi,
                $prodi->fakultas?->nama_fakultas ?? '-',
                $prodi->jenjang,
                $prodi->kaprodi ?? '-',
                $prodi->akreditasi ?? '-',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Kode', 'Nama Program Studi', 'Fakultas', 'Jenjang', 'Kaprodi', 'Akreditasi'],
            $rows
        );

        return TabularExport::excelResponse('program_studi.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $query = ProgramStudi::with('fakultas');

        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
        }

        if ($request->filled('jenjang')) {
            $query->where('jenjang', $request->jenjang);
        }

        if ($request->filled('akreditasi')) {
            $query->where('akreditasi', $request->akreditasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_prodi', 'like', "%{$search}%")
                    ->orWhere('kode_prodi', 'like', "%{$search}%")
                    ->orWhere('kaprodi', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('nama_prodi')->get();

        $rows = $items->map(function (ProgramStudi $prodi, int $index) {
            return [
                $index + 1,
                $prodi->kode_prodi,
                $prodi->nama_prodi,
                $prodi->fakultas?->nama_fakultas ?? '-',
                $prodi->jenjang,
                $prodi->kaprodi ?? '-',
                $prodi->akreditasi ?? '-',
            ];
        });

        $html = TabularExport::htmlTable(
            ['No', 'Kode', 'Nama Program Studi', 'Fakultas', 'Jenjang', 'Kaprodi', 'Akreditasi'],
            $rows
        );

        return Pdf::loadHTML($html)->download('program_studi.pdf');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        return view('program-studi.create', compact('fakultas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_prodi' => 'required|string|max:10|unique:program_studi,kode_prodi',
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|in:D3,D4,S1,S2,S3',
            'kaprodi' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Belum Terakreditasi',
        ]);

        $data = $request->except('file_ids');
        $data['created_by'] = auth()->id();

        $programStudi = ProgramStudi::create($data);

        // Handle file uploads - Link uploaded files to this record
        if ($request->filled('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                FileUpload::whereIn('id', $fileIds)
                    ->where('fileable_id', 0)
                    ->update([
                        'fileable_id' => $programStudi->id,
                        'fileable_type' => ProgramStudi::class
                    ]);
            }
        }

        return redirect()->route('program-studi.index')->with('success', 'Data program studi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProgramStudi $programStudi)
    {
        $programStudi->load('fakultas', 'files', 'mahasiswa', 'dosen');
        return view('program-studi.show', compact('programStudi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudi $programStudi)
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $programStudi->load('files');
        return view('program-studi.edit', compact('programStudi', 'fakultas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'kode_prodi' => 'required|string|max:10|unique:program_studi,kode_prodi,' . $programStudi->id,
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|in:D3,D4,S1,S2,S3',
            'kaprodi' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Belum Terakreditasi',
        ]);

        $data = $request->except('file_ids');
        $data['updated_by'] = auth()->id();

        $programStudi->update($data);

        // Handle file uploads - Link uploaded files to this record
        if ($request->filled('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                FileUpload::whereIn('id', $fileIds)
                    ->where('fileable_id', 0)
                    ->update([
                        'fileable_id' => $programStudi->id,
                        'fileable_type' => ProgramStudi::class
                    ]);
            }
        }

        return redirect()->route('program-studi.index')->with('success', 'Data program studi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        $programStudi->deleted_by = auth()->id();
        $programStudi->save();
        $programStudi->delete();

        return redirect()->route('program-studi.index')->with('success', 'Data program studi berhasil dihapus.');
    }

    /**
     * Display trashed program studi
     */
    public function trash()
    {
        $programStudi = ProgramStudi::onlyTrashed()
            ->with('fakultas')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('program-studi.trash', compact('programStudi'));
    }

    /**
     * Restore trashed program studi
     */
    public function restore($id)
    {
        $programStudi = ProgramStudi::onlyTrashed()->findOrFail($id);
        $programStudi->restore();

        return redirect()->route('program-studi.trash')->with('success', 'Data program studi berhasil dipulihkan.');
    }

    /**
     * Force delete program studi
     */
    public function forceDelete($id)
    {
        $programStudi = ProgramStudi::onlyTrashed()->findOrFail($id);

        // Delete all related files
        foreach ($programStudi->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $programStudi->forceDelete();

        return redirect()->route('program-studi.trash')->with('success', 'Data program studi berhasil dihapus permanen.');
    }
}

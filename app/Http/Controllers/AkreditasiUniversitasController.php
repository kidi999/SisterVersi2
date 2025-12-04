<?php

namespace App\Http\Controllers;

use App\Models\AkreditasiUniversitas;
use App\Models\University;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkreditasiUniversitasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AkreditasiUniversitas::with('university', 'files');

        // Filter by university
        if ($request->filled('university_id')) {
            $query->where('university_id', $request->university_id);
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
                  ->orWhereHas('university', function($q) use ($search) {
                      $q->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $akreditasi = $query->orderBy('tanggal_sk', 'desc')->paginate(10);
        $universities = University::orderBy('nama')->get();

        return view('akreditasi-universitas.index', compact('akreditasi', 'universities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $universities = University::orderBy('nama')->get();
        return view('akreditasi-universitas.create', compact('universities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasi = AkreditasiUniversitas::create([
            'university_id' => $request->university_id,
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
                    'fileable_type' => AkreditasiUniversitas::class,
                ]);
            }
        }

        return redirect()->route('akreditasi-universitas.index')
            ->with('success', 'Data akreditasi universitas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AkreditasiUniversitas $akreditasiUniversita)
    {
        $akreditasiUniversita->load('university', 'files');
        return view('akreditasi-universitas.show', compact('akreditasiUniversita'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AkreditasiUniversitas $akreditasiUniversita)
    {
        $universities = University::orderBy('nama')->get();
        $akreditasiUniversita->load('files');
        return view('akreditasi-universitas.edit', compact('akreditasiUniversita', 'universities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AkreditasiUniversitas $akreditasiUniversita)
    {
        $request->validate([
            'university_id' => 'required|exists:universities,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasiUniversita->update([
            'university_id' => $request->university_id,
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
                        'fileable_id' => $akreditasiUniversita->id,
                        'fileable_type' => AkreditasiUniversitas::class,
                    ]);
            }
        }

        return redirect()->route('akreditasi-universitas.index')
            ->with('success', 'Data akreditasi universitas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AkreditasiUniversitas $akreditasiUniversita)
    {
        $akreditasiUniversita->deleted_by = Auth::id();
        $akreditasiUniversita->save();
        $akreditasiUniversita->delete();

        return redirect()->route('akreditasi-universitas.index')
            ->with('success', 'Data akreditasi universitas berhasil dihapus.');
    }

    /**
     * Display trashed records.
     */
    public function trash()
    {
        $akreditasi = AkreditasiUniversitas::onlyTrashed()
            ->with('university')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('akreditasi-universitas.trash', compact('akreditasi'));
    }

    /**
     * Restore a trashed record.
     */
    public function restore($id)
    {
        $akreditasi = AkreditasiUniversitas::onlyTrashed()->findOrFail($id);
        $akreditasi->restore();

        return redirect()->route('akreditasi-universitas.trash')
            ->with('success', 'Data akreditasi universitas berhasil dipulihkan.');
    }

    /**
     * Permanently delete a trashed record.
     */
    public function forceDelete($id)
    {
        $akreditasi = AkreditasiUniversitas::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($akreditasi->files as $file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }
        
        $akreditasi->forceDelete();

        return redirect()->route('akreditasi-universitas.trash')
            ->with('success', 'Data akreditasi universitas berhasil dihapus permanen.');
    }
}

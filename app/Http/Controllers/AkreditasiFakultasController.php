<?php

namespace App\Http\Controllers;

use App\Models\AkreditasiFakultas;
use App\Models\Fakultas;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AkreditasiFakultasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AkreditasiFakultas::with('fakultas', 'files');

        // Filter by fakultas
        if ($request->filled('fakultas_id')) {
            $query->where('fakultas_id', $request->fakultas_id);
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
                  ->orWhereHas('fakultas', function($q) use ($search) {
                      $q->where('nama_fakultas', 'like', "%{$search}%");
                  });
            });
        }

        $akreditasi = $query->orderBy('tanggal_sk', 'desc')->paginate(10);
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();

        return view('akreditasi-fakultas.index', compact('akreditasi', 'fakultasList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        return view('akreditasi-fakultas.create', compact('fakultasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasi = AkreditasiFakultas::create([
            'fakultas_id' => $request->fakultas_id,
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
                    'fileable_type' => AkreditasiFakultas::class,
                ]);
            }
        }

        return redirect()->route('akreditasi-fakultas.index')
            ->with('success', 'Data akreditasi fakultas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AkreditasiFakultas $akreditasiFakulta)
    {
        $akreditasiFakulta->load('fakultas', 'files');
        return view('akreditasi-fakultas.show', compact('akreditasiFakulta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AkreditasiFakultas $akreditasiFakulta)
    {
        $fakultasList = Fakultas::orderBy('nama_fakultas')->get();
        $akreditasiFakulta->load('files');
        return view('akreditasi-fakultas.edit', compact('akreditasiFakulta', 'fakultasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AkreditasiFakultas $akreditasiFakulta)
    {
        $request->validate([
            'fakultas_id' => 'required|exists:fakultas,id',
            'lembaga_akreditasi' => 'required|string|max:100',
            'nomor_sk' => 'required|string|max:100',
            'tanggal_sk' => 'required|date',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_sk',
            'peringkat' => 'required|in:Unggul,Baik Sekali,Baik,A,B,C,Belum Terakreditasi',
            'tahun_akreditasi' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Kadaluarsa,Dalam Proses',
        ]);

        $akreditasiFakulta->update([
            'fakultas_id' => $request->fakultas_id,
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
                        'fileable_id' => $akreditasiFakulta->id,
                        'fileable_type' => AkreditasiFakultas::class,
                    ]);
            }
        }

        return redirect()->route('akreditasi-fakultas.index')
            ->with('success', 'Data akreditasi fakultas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AkreditasiFakultas $akreditasiFakulta)
    {
        $akreditasiFakulta->deleted_by = Auth::id();
        $akreditasiFakulta->save();
        $akreditasiFakulta->delete();

        return redirect()->route('akreditasi-fakultas.index')
            ->with('success', 'Data akreditasi fakultas berhasil dihapus.');
    }

    /**
     * Display trashed records.
     */
    public function trash()
    {
        $akreditasi = AkreditasiFakultas::onlyTrashed()
            ->with('fakultas')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('akreditasi-fakultas.trash', compact('akreditasi'));
    }

    /**
     * Restore a trashed record.
     */
    public function restore($id)
    {
        $akreditasi = AkreditasiFakultas::onlyTrashed()->findOrFail($id);
        $akreditasi->restore();

        return redirect()->route('akreditasi-fakultas.trash')
            ->with('success', 'Data akreditasi fakultas berhasil dipulihkan.');
    }

    /**
     * Permanently delete a trashed record.
     */
    public function forceDelete($id)
    {
        $akreditasi = AkreditasiFakultas::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        foreach ($akreditasi->files as $file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }
            $file->forceDelete();
        }
        
        $akreditasi->forceDelete();

        return redirect()->route('akreditasi-fakultas.trash')
            ->with('success', 'Data akreditasi fakultas berhasil dihapus permanen.');
    }
}

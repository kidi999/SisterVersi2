<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Province;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FakultasController extends Controller
{
    public function index()
    {
        $fakultas = Fakultas::with(['programStudi', 'village.subRegency.regency.province'])->get();
        return view('fakultas.index', compact('fakultas'));
    }

    public function trash()
    {
        // Only super_admin can access trash
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses data terhapus');
        }

        $fakultas = Fakultas::onlyTrashed()
            ->with(['programStudi', 'village.subRegency.regency.province'])
            ->get();
        return view('fakultas.trash', compact('fakultas'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $dosen = \App\Models\Dosen::orderBy('nama_dosen')->get();
        return view('fakultas.create', compact('provinces', 'dosen'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_fakultas' => 'required|unique:fakultas|max:10',
            'nama_fakultas' => 'required|max:100',
            'singkatan' => 'required|max:20',
            'dekan_id' => 'nullable|exists:dosen,id',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'village_id' => 'required|exists:villages,id',
            'file_ids' => 'nullable|string'
        ]);

        // Auto-fill dekan name from dosen if dekan_id is provided
        if ($request->dekan_id) {
            $dosen = \App\Models\Dosen::find($request->dekan_id);
            $validated['dekan'] = $dosen ? $dosen->nama_dosen : null;
        }

        $validated['created_by'] = Auth::user()->name;
        
        $fakultas = Fakultas::create($validated);
        
        // Update file uploads with the new fakultas_id
        if (!empty($request->file_ids) && $request->file_ids != '[]') {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                $updated = FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) {
                        $query->where('fileable_type', 'App\\Models\\Fakultas')
                              ->orWhere('fileable_id', 0);
                    })
                    ->update([
                        'fileable_id' => $fakultas->id,
                        'fileable_type' => 'App\\Models\\Fakultas',
                        'updated_by' => Auth::user()->name
                    ]);
                
                \Log::info('Files linked to Fakultas', [
                    'fakultas_id' => $fakultas->id,
                    'file_ids' => $fileIds,
                    'updated_count' => $updated
                ]);
            }
        }
        
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil ditambahkan');
    }

    public function show(Fakultas $fakultas)
    {
        $fakultas->load(['programStudi', 'village.subRegency.regency.province', 'files', 'dekanAktif', 'riwayatDekan.dosen']);
        return view('fakultas.show', compact('fakultas'));
    }

    public function edit(Fakultas $fakultas)
    {
        $provinces = Province::orderBy('name')->get();
        $dosen = \App\Models\Dosen::orderBy('nama_dosen')->get();
        $fakultas->load('village.subRegency.regency.province', 'files', 'dekanAktif', 'riwayatDekan.dosen');
        return view('fakultas.edit', compact('fakultas', 'provinces', 'dosen'));
    }

    public function update(Request $request, Fakultas $fakultas)
    {
        $validated = $request->validate([
            'kode_fakultas' => 'required|max:10|unique:fakultas,kode_fakultas,' . $fakultas->id,
            'nama_fakultas' => 'required|max:100',
            'singkatan' => 'required|max:20',
            'dekan_id' => 'nullable|exists:dosen,id',
            'alamat' => 'nullable',
            'telepon' => 'nullable|max:20',
            'email' => 'nullable|email|max:100',
            'village_id' => 'required|exists:villages,id',
            'file_ids' => 'nullable|string'
        ]);

        // Auto-fill dekan name from dosen if dekan_id is provided
        if ($request->dekan_id) {
            $dosen = \App\Models\Dosen::find($request->dekan_id);
            $validated['dekan'] = $dosen ? $dosen->nama_dosen : null;
        } else {
            $validated['dekan'] = null;
        }

        $validated['updated_by'] = Auth::user()->name;
        
        $fakultas->update($validated);

        // Update file associations if file_ids provided
        if (!empty($request->file_ids) && $request->file_ids != '[]') {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                // Update all files in the list to be linked to this fakultas
                FileUpload::whereIn('id', $fileIds)
                    ->update([
                        'fileable_id' => $fakultas->id,
                        'fileable_type' => 'App\\Models\\Fakultas',
                        'updated_by' => Auth::user()->name
                    ]);
                
                // Remove files that are no longer in the list (were deleted)
                FileUpload::where('fileable_id', $fakultas->id)
                    ->where('fileable_type', 'App\\Models\\Fakultas')
                    ->whereNotIn('id', $fileIds)
                    ->delete();
                
                \Log::info('Files synced for Fakultas', [
                    'fakultas_id' => $fakultas->id,
                    'file_ids' => $fileIds
                ]);
            }
        } else {
            // If no files provided, remove all existing files
            FileUpload::where('fileable_id', $fakultas->id)
                ->where('fileable_type', 'App\\Models\\Fakultas')
                ->delete();
        }

        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil diperbarui');
    }

    public function destroy(Fakultas $fakultas)
    {
        $fakultas->deleted_by = Auth::user()->name;
        $fakultas->save();
        $fakultas->delete(); // Soft delete
        return redirect()->route('fakultas.index')->with('success', 'Fakultas berhasil dihapus (soft delete)');
    }

    public function restore($id)
    {
        // Only super_admin can restore
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat restore data');
        }

        $fakultas = Fakultas::onlyTrashed()->findOrFail($id);
        $fakultas->restore();
        
        return redirect()->route('fakultas.trash')->with('success', 'Fakultas berhasil di-restore');
    }

    public function forceDelete($id)
    {
        // Only super_admin can permanently delete
        if (!Auth::user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat menghapus permanen');
        }

        $fakultas = Fakultas::onlyTrashed()->findOrFail($id);
        $fakultas->forceDelete();
        
        return redirect()->route('fakultas.trash')->with('success', 'Fakultas berhasil dihapus permanen');
    }
}

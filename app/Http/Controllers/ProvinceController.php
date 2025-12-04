<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::withCount('regencies')->orderBy('name')->get();
        return view('wilayah.provinsi.index', compact('provinces'));
    }

    public function create()
    {
        return view('wilayah.provinsi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:provinces|max:10',
            'name' => 'required|max:100'
        ]);

        $validated['created_by'] = Auth::user()->name;
        
        $province = Province::create($validated);

        // Link uploaded files to this province
        if ($request->has('file_ids') && !empty($request->file_ids)) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds) && count($fileIds) > 0) {
                FileUpload::whereIn('id', $fileIds)
                    ->update([
                        'fileable_type' => Province::class,
                        'fileable_id' => $province->id
                    ]);
            }
        }
        
        return redirect()->route('provinsi.index')->with('success', 'Provinsi berhasil ditambahkan');
    }

    public function show(Province $province)
    {
        $province->load(['regencies', 'files']);
        return view('wilayah.provinsi.show', compact('province'));
    }

    public function edit(Province $province)
    {
        $province->load(['files']);
        return view('wilayah.provinsi.edit', compact('province'));
    }

    public function update(Request $request, Province $province)
    {
        $validated = $request->validate([
            'code' => 'required|max:10|unique:provinces,code,' . $province->id,
            'name' => 'required|max:100'
        ]);

        $validated['updated_by'] = Auth::user()->name;
        
        $province->update($validated);

        // Sync files: update existing ones and link new ones
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds)) {
                // Update all files in the list to be linked to this province
                FileUpload::whereIn('id', $fileIds)
                    ->update([
                        'fileable_type' => Province::class,
                        'fileable_id' => $province->id
                    ]);

                // Remove orphaned files (files that were previously linked but not in the new list)
                FileUpload::where('fileable_type', Province::class)
                    ->where('fileable_id', $province->id)
                    ->whereNotIn('id', $fileIds)
                    ->delete();
            }
        }

        return redirect()->route('provinsi.index')->with('success', 'Provinsi berhasil diperbarui');
    }

    public function destroy(Province $province)
    {
        $province->deleted_by = Auth::user()->name;
        $province->save();
        $province->delete();
        
        return redirect()->route('provinsi.index')->with('success', 'Provinsi berhasil dihapus');
    }

    public function trash()
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Hanya Super Admin yang dapat mengakses halaman ini');
        }

        $provinces = Province::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();
        return view('wilayah.provinsi.trash', compact('provinces'));
    }

    public function restore($id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Hanya Super Admin yang dapat restore data');
        }

        $province = Province::onlyTrashed()->findOrFail($id);
        $province->restore();
        
        return redirect()->route('provinsi.trash')->with('success', 'Provinsi berhasil di-restore');
    }

    public function forceDelete($id)
    {
        if (!Auth::user()->hasRole(['super_admin'])) {
            abort(403, 'Hanya Super Admin yang dapat menghapus permanen');
        }

        $province = Province::onlyTrashed()->findOrFail($id);
        $province->forceDelete();
        
        return redirect()->route('provinsi.trash')->with('success', 'Provinsi berhasil dihapus permanen');
    }
}

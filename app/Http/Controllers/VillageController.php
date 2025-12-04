<?php

namespace App\Http\Controllers;

use App\Models\Village;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VillageController extends Controller
{
    public function index(Request $request)
    {
        $query = Village::with(['subRegency.regency.province']);

        // Filter by province
        if ($request->filled('province_id')) {
            $query->whereHas('subRegency.regency', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        // Filter by regency
        if ($request->filled('regency_id')) {
            $query->whereHas('subRegency', function($q) use ($request) {
                $q->where('regency_id', $request->regency_id);
            });
        }

        // Filter by sub_regency
        if ($request->filled('sub_regency_id')) {
            $query->where('sub_regency_id', $request->sub_regency_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('postal_code', 'like', "%{$search}%");
            });
        }

        $villages = $query->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        $subRegencies = SubRegency::orderBy('name')->get();

        return view('village.index', compact('villages', 'provinces', 'regencies', 'subRegencies'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        $subRegencies = SubRegency::orderBy('name')->get();
        
        return view('village.create', compact('provinces', 'regencies', 'subRegencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sub_regency_id' => 'required|exists:sub_regencies,id',
            'code' => 'required|string|max:10|unique:villages,code',
            'name' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ], [
            'sub_regency_id.required' => 'Kecamatan harus dipilih',
            'sub_regency_id.exists' => 'Kecamatan tidak valid',
            'code.required' => 'Kode desa/kelurahan harus diisi',
            'code.unique' => 'Kode desa/kelurahan sudah digunakan',
            'name.required' => 'Nama desa/kelurahan harus diisi',
        ]);

        $validated['created_by'] = Auth::user()->name;

        $village = Village::create($validated);

        // Handle new file uploads
        if ($request->has('file_ids')) {
            $fileIds = is_array($request->file_ids) ? $request->file_ids : json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0);
                    })
                    ->update([
                        'fileable_id' => $village->id,
                        'fileable_type' => 'App\\Models\\Village'
                    ]);
            }
        }

        return redirect()->route('village.index')
            ->with('success', 'Desa/Kelurahan berhasil ditambahkan.');
    }

    public function show(Village $village)
    {
        $village->load(['subRegency.regency.province', 'files']);
        return view('village.show', compact('village'));
    }

    public function edit(Village $village)
    {
        $village->load('files');
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        $subRegencies = SubRegency::orderBy('name')->get();
        
        return view('village.edit', compact('village', 'provinces', 'regencies', 'subRegencies'));
    }

    public function update(Request $request, Village $village)
    {
        $validated = $request->validate([
            'sub_regency_id' => 'required|exists:sub_regencies,id',
            'code' => 'required|string|max:10|unique:villages,code,' . $village->id,
            'name' => 'required|string|max:100',
            'postal_code' => 'nullable|string|max:10',
        ], [
            'sub_regency_id.required' => 'Kecamatan harus dipilih',
            'sub_regency_id.exists' => 'Kecamatan tidak valid',
            'code.required' => 'Kode desa/kelurahan harus diisi',
            'code.unique' => 'Kode desa/kelurahan sudah digunakan',
            'name.required' => 'Nama desa/kelurahan harus diisi',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $village->update($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) use ($village) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0)
                              ->orWhere('fileable_id', $village->id);
                    })
                    ->update([
                        'fileable_id' => $village->id,
                        'fileable_type' => 'App\\Models\\Village'
                    ]);
            }
        }

        return redirect()->route('village.index')
            ->with('success', 'Desa/Kelurahan berhasil diperbarui.');
    }

    public function destroy(Village $village)
    {
        $village->deleted_by = Auth::user()->name;
        $village->save();
        $village->delete();

        return redirect()->route('village.index')
            ->with('success', 'Desa/Kelurahan berhasil dihapus.');
    }

    public function trash()
    {
        $villages = Village::onlyTrashed()
            ->with(['subRegency.regency.province'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('village.trash', compact('villages'));
    }

    public function restore($id)
    {
        $village = Village::onlyTrashed()->findOrFail($id);
        $village->restore();

        return response()->json([
            'message' => 'Desa/Kelurahan berhasil dipulihkan.'
        ]);
    }

    public function forceDelete($id)
    {
        $village = Village::onlyTrashed()->findOrFail($id);
        
        // Delete all files
        foreach ($village->files as $file) {
            if (\Storage::exists($file->file_path)) {
                \Storage::delete($file->file_path);
            }
            $file->delete();
        }
        
        $village->forceDelete();

        return response()->json([
            'message' => 'Desa/Kelurahan berhasil dihapus permanen.'
        ]);
    }

    public function getSubRegenciesByRegency($regencyId)
    {
        $subRegencies = SubRegency::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($subRegencies);
    }
}

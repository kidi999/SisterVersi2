<?php

namespace App\Http\Controllers;

use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubRegencyController extends Controller
{
    public function index(Request $request)
    {
        $query = SubRegency::with(['regency.province']);

        // Filter by province
        if ($request->filled('province_id')) {
            $query->whereHas('regency', function($q) use ($request) {
                $q->where('province_id', $request->province_id);
            });
        }

        // Filter by regency
        if ($request->filled('regency_id')) {
            $query->where('regency_id', $request->regency_id);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $subRegencies = $query->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();

        return view('sub-regency.index', compact('subRegencies', 'provinces', 'regencies'));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        
        return view('sub-regency.create', compact('provinces', 'regencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'required|string|max:10|unique:sub_regencies,code',
            'name' => 'required|string|max:100',
        ], [
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'code.required' => 'Kode kecamatan harus diisi',
            'code.unique' => 'Kode kecamatan sudah digunakan',
            'name.required' => 'Nama kecamatan harus diisi',
        ]);

        $validated['created_by'] = Auth::user()->name;

        $subRegency = SubRegency::create($validated);

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
                        'fileable_id' => $subRegency->id,
                        'fileable_type' => 'App\\Models\\SubRegency'
                    ]);
            }
        }

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil ditambahkan.');
    }

    public function show(SubRegency $subRegency)
    {
        $subRegency->load(['regency.province', 'villages', 'files']);
        return view('sub-regency.show', compact('subRegency'));
    }

    public function edit(SubRegency $subRegency)
    {
        $subRegency->load('files');
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::orderBy('name')->get();
        
        return view('sub-regency.edit', compact('subRegency', 'provinces', 'regencies'));
    }

    public function update(Request $request, SubRegency $subRegency)
    {
        $validated = $request->validate([
            'regency_id' => 'required|exists:regencies,id',
            'code' => 'required|string|max:10|unique:sub_regencies,code,' . $subRegency->id,
            'name' => 'required|string|max:100',
        ], [
            'regency_id.required' => 'Kabupaten/Kota harus dipilih',
            'regency_id.exists' => 'Kabupaten/Kota tidak valid',
            'code.required' => 'Kode kecamatan harus diisi',
            'code.unique' => 'Kode kecamatan sudah digunakan',
            'name.required' => 'Nama kecamatan harus diisi',
        ]);

        $validated['updated_by'] = Auth::user()->name;

        $subRegency->update($validated);

        // Handle file uploads
        if ($request->has('file_ids')) {
            $fileIds = json_decode($request->file_ids, true);
            if (is_array($fileIds) && !empty($fileIds)) {
                \App\Models\FileUpload::whereIn('id', $fileIds)
                    ->where(function($query) use ($subRegency) {
                        $query->whereNull('fileable_id')
                              ->orWhere('fileable_id', 0)
                              ->orWhere('fileable_id', $subRegency->id);
                    })
                    ->update([
                        'fileable_id' => $subRegency->id,
                        'fileable_type' => 'App\\Models\\SubRegency'
                    ]);
            }
        }

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil diperbarui.');
    }

    public function destroy(SubRegency $subRegency)
    {
        $subRegency->deleted_by = Auth::user()->name;
        $subRegency->save();
        $subRegency->delete();

        return redirect()->route('sub-regency.index')
            ->with('success', 'Kecamatan berhasil dihapus.');
    }

    public function trash()
    {
        $subRegencies = SubRegency::onlyTrashed()
            ->with(['regency.province'])
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('sub-regency.trash', compact('subRegencies'));
    }

    public function restore($id)
    {
        $subRegency = SubRegency::onlyTrashed()->findOrFail($id);
        $subRegency->restore();

        return response()->json([
            'message' => 'Kecamatan berhasil dipulihkan.'
        ]);
    }

    public function forceDelete($id)
    {
        $subRegency = SubRegency::onlyTrashed()->findOrFail($id);
        
        // Delete all files
        foreach ($subRegency->files as $file) {
            if (\Storage::exists($file->file_path)) {
                \Storage::delete($file->file_path);
            }
            $file->delete();
        }
        
        $subRegency->forceDelete();

        return response()->json([
            'message' => 'Kecamatan berhasil dihapus permanen.'
        ]);
    }

    public function getRegenciesByProvince($provinceId)
    {
        $regencies = Regency::where('province_id', $provinceId)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);

        return response()->json($regencies);
    }
}

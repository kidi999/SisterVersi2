<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubRegency;
use App\Models\Village;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UniversityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = University::with('village.subRegency.regency.province');

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('akreditasi')) {
            $query->where('akreditasi', $request->akreditasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  ->orWhere('singkatan', 'like', "%{$search}%");
            });
        }

        $universities = $query->orderBy('nama')->paginate(10);

        return view('university.index', compact('universities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('university.create', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:universities,kode',
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
            'jenis' => 'required|in:Negeri,Swasta',
            'status' => 'required|in:Aktif,Nonaktif',
            'akreditasi' => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Belum Terakreditasi',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'village_id' => 'nullable|exists:villages,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('logo', 'file_upload');
        $data['created_by'] = auth()->id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('logos', $logoName, 'public');
            $data['logo_path'] = $logoPath;
            $data['nama_file_logo'] = $logo->getClientOriginalName();
        }

        $university = University::create($data);

        // Handle file uploads
        if ($request->hasFile('file_upload')) {
            foreach ($request->file('file_upload') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('university_documents', $filename, 'public');

                FileUpload::create([
                    'fileable_type' => University::class,
                    'fileable_id' => $university->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('universities.index')->with('success', 'Data universitas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(University $university)
    {
        $university->load('village.subRegency.regency.province', 'files');
        return view('university.show', compact('university'));
    }

    /**
     * Display university profile (public facing)
     */
    public function profile()
    {
        $university = University::where('status', 'Aktif')
            ->with('village.subRegency.regency.province')
            ->first();

        if (!$university) {
            abort(404, 'Data universitas tidak ditemukan.');
        }

        return view('university.profile', compact('university'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(University $university)
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = $university->village ? Regency::where('province_id', $university->village->subRegency->regency->province_id)->orderBy('name')->get() : collect();
        $subRegencies = $university->village ? SubRegency::where('regency_id', $university->village->subRegency->regency_id)->orderBy('name')->get() : collect();
        $villages = $university->village ? Village::where('sub_regency_id', $university->village->sub_regency_id)->orderBy('name')->get() : collect();

        $university->load('files');

        return view('university.edit', compact('university', 'provinces', 'regencies', 'subRegencies', 'villages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, University $university)
    {
        $request->validate([
            'kode' => 'required|string|max:20|unique:universities,kode,' . $university->id,
            'nama' => 'required|string|max:255',
            'singkatan' => 'nullable|string|max:50',
            'jenis' => 'required|in:Negeri,Swasta',
            'status' => 'required|in:Aktif,Nonaktif',
            'akreditasi' => 'nullable|in:A,B,C,Unggul,Baik Sekali,Baik,Belum Terakreditasi',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'village_id' => 'nullable|exists:villages,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('logo', 'file_upload');
        $data['updated_by'] = auth()->id();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($university->logo_path) {
                Storage::disk('public')->delete($university->logo_path);
            }

            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logoPath = $logo->storeAs('logos', $logoName, 'public');
            $data['logo_path'] = $logoPath;
            $data['nama_file_logo'] = $logo->getClientOriginalName();
        }

        $university->update($data);

        // Handle new file uploads
        if ($request->hasFile('file_upload')) {
            foreach ($request->file('file_upload') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('university_documents', $filename, 'public');

                FileUpload::create([
                    'fileable_type' => University::class,
                    'fileable_id' => $university->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getClientMimeType(),
                    'created_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('universities.index')->with('success', 'Data universitas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(University $university)
    {
        $university->deleted_by = auth()->id();
        $university->save();
        $university->delete();

        return redirect()->route('universities.index')->with('success', 'Data universitas berhasil dihapus.');
    }

    /**
     * Display trashed universities
     */
    public function trash()
    {
        $universities = University::onlyTrashed()
            ->with('village.subRegency.regency.province')
            ->orderBy('deleted_at', 'desc')
            ->paginate(10);

        return view('university.trash', compact('universities'));
    }

    /**
     * Restore trashed university
     */
    public function restore($id)
    {
        $university = University::onlyTrashed()->findOrFail($id);
        $university->restore();

        return redirect()->route('universities.trash')->with('success', 'Data universitas berhasil dipulihkan.');
    }

    /**
     * Force delete university
     */
    public function forceDelete($id)
    {
        $university = University::onlyTrashed()->findOrFail($id);

        // Delete logo if exists
        if ($university->logo_path) {
            Storage::disk('public')->delete($university->logo_path);
        }

        // Delete all related files
        foreach ($university->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $university->forceDelete();

        return redirect()->route('universities.trash')->with('success', 'Data universitas berhasil dihapus permanen.');
    }
}

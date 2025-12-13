<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\FileUpload;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubRegency;
use App\Models\Village;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilMahasiswaController extends Controller
{
    /**
     * Display mahasiswa profile
     */
    public function index()
    {
        $user = Auth::user();
        
        // Only mahasiswa can access
        if (!$user->hasRole('mahasiswa')) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Halaman ini hanya untuk mahasiswa.');
        }
        
        if (!$user->mahasiswa_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }
        
        $mahasiswa = Mahasiswa::with([
            'programStudi.fakultas',
            'village.subRegency.regency.province',
            'files'
        ])->findOrFail($user->mahasiswa_id);
        
        return view('profil-mahasiswa.index', compact('mahasiswa'));
    }

    public function exportExcel(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('mahasiswa') || !$user->mahasiswa_id) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $mahasiswa = Mahasiswa::with([
            'programStudi.fakultas',
            'village.subRegency.regency.province',
            'files'
        ])->findOrFail($user->mahasiswa_id);

        $headers = ['Field', 'Value'];
        $rows = [
            ['Nama', $mahasiswa->nama_mahasiswa],
            ['NIM', $mahasiswa->nim],
            ['Email', $mahasiswa->email],
            ['Telepon', $mahasiswa->telepon ?? '-'],
            ['Alamat', $mahasiswa->alamat ?? '-'],
            ['Program Studi', $mahasiswa->programStudi->nama_prodi ?? '-'],
            ['Fakultas', $mahasiswa->programStudi->fakultas->nama_fakultas ?? '-'],
            ['Tahun Masuk', $mahasiswa->tahun_masuk ?? '-'],
            ['Semester', (string) ($mahasiswa->semester ?? '-')],
            ['IPK', (string) ($mahasiswa->ipk ?? '-')],
            ['Status', $mahasiswa->status ?? '-'],
            ['Lampiran', (string) $mahasiswa->files->count()],
        ];

        $html = TabularExport::htmlTable($headers, $rows);
        return TabularExport::excelResponse('profil_mahasiswa.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('mahasiswa') || !$user->mahasiswa_id) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $mahasiswa = Mahasiswa::with([
            'programStudi.fakultas',
            'village.subRegency.regency.province',
            'files'
        ])->findOrFail($user->mahasiswa_id);

        $headers = ['Field', 'Value'];
        $rows = [
            ['Nama', $mahasiswa->nama_mahasiswa],
            ['NIM', $mahasiswa->nim],
            ['Email', $mahasiswa->email],
            ['Telepon', $mahasiswa->telepon ?? '-'],
            ['Alamat', $mahasiswa->alamat ?? '-'],
            ['Program Studi', $mahasiswa->programStudi->nama_prodi ?? '-'],
            ['Fakultas', $mahasiswa->programStudi->fakultas->nama_fakultas ?? '-'],
            ['Tahun Masuk', $mahasiswa->tahun_masuk ?? '-'],
            ['Semester', (string) ($mahasiswa->semester ?? '-')],
            ['IPK', (string) ($mahasiswa->ipk ?? '-')],
            ['Status', $mahasiswa->status ?? '-'],
            ['Lampiran', (string) $mahasiswa->files->count()],
        ];

        $html = TabularExport::htmlTable($headers, $rows);
        return Pdf::loadHTML($html)->download('profil_mahasiswa.pdf');
    }
    
    /**
     * Show edit form
     */
    public function edit()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('mahasiswa')) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak.');
        }
        
        if (!$user->mahasiswa_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }
        
        $mahasiswa = Mahasiswa::with([
            'programStudi.fakultas',
            'village.subRegency.regency.province',
            'files'
        ])->findOrFail($user->mahasiswa_id);
        
        // Get data untuk dropdown
        $provinsiList = Province::orderBy('name')->get();
        
        // Get data wilayah yang sudah dipilih
        $selectedProvinsi = null;
        $selectedRegency = null;
        $selectedSubRegency = null;
        $regencyList = collect([]);
        $subRegencyList = collect([]);
        $villageList = collect([]);
        
        if ($mahasiswa->village_id) {
            $village = Village::with('subRegency.regency.province')->find($mahasiswa->village_id);
            if ($village) {
                $selectedProvinsi = $village->subRegency->regency->province_id;
                $selectedRegency = $village->subRegency->regency_id;
                $selectedSubRegency = $village->sub_regency_id;
                
                $regencyList = Regency::where('province_id', $selectedProvinsi)->orderBy('name')->get();
                $subRegencyList = SubRegency::where('regency_id', $selectedRegency)->orderBy('name')->get();
                $villageList = Village::where('sub_regency_id', $selectedSubRegency)->orderBy('name')->get();
            }
        }
        
        return view('profil-mahasiswa.edit', compact(
            'mahasiswa',
            'provinsiList',
            'regencyList',
            'subRegencyList',
            'villageList',
            'selectedProvinsi',
            'selectedRegency',
            'selectedSubRegency'
        ));
    }
    
    /**
     * Update mahasiswa profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->mahasiswa_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan.');
        }
        
        $mahasiswa = Mahasiswa::findOrFail($user->mahasiswa_id);
        
        $validated = $request->validate([
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'village_id' => 'required|exists:village,id',
            'telepon' => 'required|string|max:20',
            'email' => 'required|email|unique:mahasiswa,email,' . $mahasiswa->id,
            'nama_wali' => 'nullable|string|max:100',
            'telepon_wali' => 'nullable|string|max:20',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id',
        ]);
        
        $mahasiswa->update([
            'jenis_kelamin' => $validated['jenis_kelamin'],
            'tempat_lahir' => $validated['tempat_lahir'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'alamat' => $validated['alamat'],
            'village_id' => $validated['village_id'],
            'telepon' => $validated['telepon'],
            'email' => $validated['email'],
            'nama_wali' => $validated['nama_wali'],
            'telepon_wali' => $validated['telepon_wali'],
            'updated_by' => $user->name,
        ]);

        if ($request->filled('file_ids')) {
            FileUpload::whereIn('id', $request->file_ids)
                ->update([
                    'fileable_type' => Mahasiswa::class,
                    'fileable_id' => $mahasiswa->id,
                ]);
        }
        
        return redirect()->route('profil-mahasiswa.index')
            ->with('success', 'Profil berhasil diperbarui');
    }
    
    /**
     * Show change password form
     */
    public function editPassword()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('mahasiswa')) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak.');
        }
        
        return view('profil-mahasiswa.change-password');
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);
        
        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai'])
                ->withInput();
        }
        
        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
            'updated_by' => $user->name,
        ]);
        
        return redirect()->route('profil-mahasiswa.index')
            ->with('success', 'Password berhasil diubah');
    }
    
    /**
     * Get regencies by provinsi (AJAX)
     */
    public function getRegencies($provinsiId)
    {
        $regencies = Regency::where('province_id', $provinsiId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($regencies);
    }
    
    /**
     * Get sub regencies by regency (AJAX)
     */
    public function getSubRegencies($regencyId)
    {
        $subRegencies = SubRegency::where('regency_id', $regencyId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($subRegencies);
    }
    
    /**
     * Get villages by sub regency (AJAX)
     */
    public function getVillages($subRegencyId)
    {
        $villages = Village::where('sub_regency_id', $subRegencyId)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($villages);
    }
}

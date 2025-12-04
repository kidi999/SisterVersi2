<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranMahasiswa;
use App\Models\ProgramStudi;
use App\Models\Province;
use App\Models\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PmbController extends Controller
{
    /**
     * Tampilkan halaman utama PMB
     */
    public function index()
    {
        return view('pmb.index');
    }

    /**
     * Tampilkan form pendaftaran
     */
    public function create()
    {
        $programStudi = ProgramStudi::with('fakultas')
            ->orderBy('nama_prodi')
            ->get();
        $provinces = Province::orderBy('name')->get();
        
        // Generate tahun akademik otomatis
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $tahunAkademik = "$currentYear/$nextYear";
        
        return view('pmb.form', compact('programStudi', 'provinces', 'tahunAkademik'));
    }

    /**
     * Simpan pendaftaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tahun_akademik' => 'required|string|max:10',
            'jalur_masuk' => 'required|in:SNBP,SNBT,Mandiri,Transfer',
            'program_studi_id' => 'required|exists:program_studi,id',
            'nama_lengkap' => 'required|string|max:100',
            'nik' => 'nullable|string|max:16',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'nullable|string|max:50',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:20',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai',
            'kewarganegaraan' => 'nullable|string|max:30',
            'alamat' => 'nullable|string',
            'village_id' => 'nullable|exists:villages,id',
            'kode_pos' => 'nullable|string|max:10',
            'telepon' => 'nullable|string|max:20',
            'email' => 'required|email|max:100',
            'asal_sekolah' => 'nullable|string|max:100',
            'jurusan_sekolah' => 'nullable|string|max:50',
            'tahun_lulus' => 'nullable|string|max:4',
            'nilai_rata_rata' => 'nullable|numeric|min:0|max:100',
            'nama_ayah' => 'nullable|string|max:100',
            'pekerjaan_ayah' => 'nullable|string|max:50',
            'nama_ibu' => 'nullable|string|max:100',
            'pekerjaan_ibu' => 'nullable|string|max:50',
            'nama_wali' => 'nullable|string|max:100',
            'telepon_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'file_ids' => 'nullable|array',
            'file_ids.*' => 'exists:file_uploads,id'
        ]);

        DB::beginTransaction();
        try {
            // Generate nomor pendaftaran
            $validated['no_pendaftaran'] = PendaftaranMahasiswa::generateNoPendaftaran(
                $validated['tahun_akademik'],
                $validated['program_studi_id']
            );

            // Status default untuk pendaftaran publik
            $validated['status'] = 'Pending';

            $pendaftaran = PendaftaranMahasiswa::create($validated);

            // Attach files
            if ($request->filled('file_ids')) {
                FileUpload::whereIn('id', $request->file_ids)
                    ->update([
                        'fileable_type' => PendaftaranMahasiswa::class,
                        'fileable_id' => $pendaftaran->id
                    ]);
            }

            DB::commit();

            return redirect()->route('pmb.success', $pendaftaran->id)
                ->with('success', 'Pendaftaran berhasil! Nomor pendaftaran Anda: ' . $pendaftaran->no_pendaftaran);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman sukses setelah pendaftaran
     */
    public function success($id)
    {
        $pendaftaran = PendaftaranMahasiswa::with(['programStudi.fakultas'])->findOrFail($id);
        return view('pmb.success', compact('pendaftaran'));
    }

    /**
     * Cek status pendaftaran
     */
    public function checkStatus(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('pmb.check-status');
        }

        $request->validate([
            'no_pendaftaran' => 'required|string',
            'email' => 'required|email'
        ]);

        $pendaftaran = PendaftaranMahasiswa::where('no_pendaftaran', $request->no_pendaftaran)
            ->where('email', $request->email)
            ->with(['programStudi.fakultas', 'verifikator'])
            ->first();

        if (!$pendaftaran) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pendaftaran tidak ditemukan. Periksa kembali nomor pendaftaran dan email Anda.');
        }

        return view('pmb.status', compact('pendaftaran'));
    }
}

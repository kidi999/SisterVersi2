<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\JadwalKuliah;
use App\Models\Semester;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        
        // Initialize data variables
        $mahasiswaData = null;
        $dosenData = null;
        $mataKuliahData = [];
        $jadwalData = [];
        $semesterAktif = null;
        $statistikAkademik = null;
        
        // Jika user adalah mahasiswa
        if ($user->mahasiswa_id && $user->mahasiswa) {
            $mahasiswa = $user->mahasiswa;
            
            // Get semester aktif
            $semesterAktif = Semester::where('is_active', true)
                ->where(function($q) use ($mahasiswa) {
                    $q->whereNull('program_studi_id')
                      ->orWhere('program_studi_id', $mahasiswa->program_studi_id);
                })
                ->first();
            
            // Data mahasiswa
            $mahasiswaData = [
                'nim' => $mahasiswa->nim,
                'nama' => $mahasiswa->nama_mahasiswa,
                'program_studi' => $mahasiswa->programStudi->nama_prodi ?? '-',
                'fakultas' => $mahasiswa->programStudi->fakultas->nama_fakultas ?? '-',
                'semester' => $mahasiswa->semester ?? 1,
                'status' => $mahasiswa->status ?? 'Aktif',
                'tahun_masuk' => $mahasiswa->tahun_masuk,
                'ipk' => $mahasiswa->ipk ?? 0.00,
            ];
            
            // Get KRS semester aktif dengan nilai
            if ($semesterAktif) {
                $krsList = Krs::where('mahasiswa_id', $mahasiswa->id)
                    ->where('tahun_ajaran', $semesterAktif->tahunAkademik->tahun_ajaran ?? date('Y'))
                    ->where('semester', $semesterAktif->nama_semester ?? 'Ganjil')
                    ->where('status', 'Disetujui')
                    ->with([
                        'kelas.mataKuliah',
                        'kelas.dosen',
                        'nilai'
                    ])
                    ->get();
                
                // Prepare mata kuliah data dengan nilai
                foreach ($krsList as $krs) {
                    if ($krs->kelas && $krs->kelas->mataKuliah) {
                        $mataKuliahData[] = [
                            'kode' => $krs->kelas->mataKuliah->kode_mk,
                            'nama' => $krs->kelas->mataKuliah->nama_mk,
                            'sks' => $krs->kelas->mataKuliah->sks,
                            'dosen' => $krs->kelas->dosen->nama_dosen ?? '-',
                            'nilai_huruf' => $krs->nilai->nilai_huruf ?? '-',
                            'nilai_angka' => $krs->nilai->nilai_akhir ?? '-',
                            'bobot' => $krs->nilai->bobot ?? '-',
                        ];
                    }
                }
                
                // Get jadwal kuliah semester aktif
                $kelasIds = $krsList->pluck('kelas_id')->toArray();
                
                if (!empty($kelasIds)) {
                    $jadwalData = JadwalKuliah::whereIn('kelas_id', $kelasIds)
                        ->where('semester_id', $semesterAktif->id)
                        ->with([
                            'kelas.mataKuliah',
                            'kelas.dosen',
                            'ruang'
                        ])
                        ->orderBy('hari')
                        ->orderBy('jam_mulai')
                        ->get()
                        ->map(function($jadwal) {
                            return [
                                'hari' => $jadwal->hari,
                                'jam_mulai' => substr($jadwal->jam_mulai, 0, 5),
                                'jam_selesai' => substr($jadwal->jam_selesai, 0, 5),
                                'mata_kuliah' => $jadwal->kelas->mataKuliah->nama_mk ?? '-',
                                'kode_mk' => $jadwal->kelas->mataKuliah->kode_mk ?? '-',
                                'sks' => $jadwal->kelas->mataKuliah->sks ?? 0,
                                'dosen' => $jadwal->kelas->dosen->nama_dosen ?? '-',
                                'ruang' => $jadwal->ruang->nama_ruang ?? '-',
                                'kelas' => $jadwal->kelas->nama_kelas ?? '-',
                            ];
                        })
                        ->toArray();
                }
                
                // Hitung statistik akademik
                $totalSks = collect($mataKuliahData)->sum('sks');
                $totalNilai = Nilai::whereHas('krs', function($q) use ($mahasiswa) {
                    $q->where('mahasiswa_id', $mahasiswa->id)
                      ->where('status', 'Disetujui');
                })->count();
                
                $statistikAkademik = [
                    'total_sks_semester' => $totalSks,
                    'total_mk_semester' => count($mataKuliahData),
                    'total_nilai_input' => $totalNilai,
                    'semester_aktif' => $semesterAktif->nama_semester ?? '-',
                    'tahun_ajaran' => $semesterAktif->tahunAkademik->tahun_ajaran ?? '-',
                ];
            }
        }
        
        // Jika user adalah dosen
        if ($user->dosen_id && $user->dosen) {
            $dosen = $user->dosen;
            
            $dosenData = [
                'nidn' => $dosen->nidn,
                'nama' => $dosen->nama_dosen,
                'gelar_depan' => $dosen->gelar_depan,
                'gelar_belakang' => $dosen->gelar_belakang,
                'fakultas' => $dosen->fakultas->nama_fakultas ?? '-',
                'email' => $dosen->email,
                'telepon' => $dosen->telepon,
            ];
        }
        
        return view('profile.edit', compact(
            'user', 
            'mahasiswaData', 
            'dosenData',
            'mataKuliahData', 
            'jadwalData', 
            'semesterAktif',
            'statistikAkademik'
        ));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui');
    }

    public function editPassword()
    {
        return view('profile.edit-password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.edit-password')->with('success', 'Password berhasil diubah');
    }
}

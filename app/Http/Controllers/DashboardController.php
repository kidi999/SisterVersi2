<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Semester;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Dashboard untuk Mahasiswa
        if ($user->hasRole('mahasiswa')) {
            $mahasiswa = Mahasiswa::with(['programStudi.fakultas'])
                ->where('id', $user->mahasiswa_id)
                ->first();
            
            if (!$mahasiswa) {
                return redirect()->route('logout')
                    ->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
            }
            
            // Semester aktif
            $semesterAktif = Semester::where('is_active', true)->first();
            
            // Total SKS yang sudah diambil (lulus)
            $nilaiLulus = Nilai::with(['krs.kelas.mataKuliah'])
                ->whereHas('krs', function($q) use ($user) {
                    $q->where('mahasiswa_id', $user->mahasiswa_id);
                })
                ->whereIn('nilai_huruf', ['A', 'A-', 'B+', 'B', 'B-', 'C+', 'C'])
                ->get();
            
            $totalSksLulus = $nilaiLulus->sum(function($nilai) {
                return $nilai->krs->kelas->mataKuliah->sks ?? 0;
            });
            
            // KRS semester ini
            $krsAktif = collect([]);
            $totalSksSemesterIni = 0;
            if ($semesterAktif) {
                // Ambil tahun ajaran dan semester dari semester aktif
                // Format: 2024/2025 Ganjil
                $parts = explode(' ', $semesterAktif->nama_semester);
                $tahunAjaran = $parts[0] ?? '';
                $semesterType = $parts[1] ?? '';
                
                $krsAktif = Krs::with(['kelas.mataKuliah', 'kelas.dosen'])
                    ->where('mahasiswa_id', $user->mahasiswa_id)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semesterType)
                    ->where('status', 'Disetujui')
                    ->get();
                
                $totalSksSemesterIni = $krsAktif->sum(function($krs) {
                    return $krs->kelas->mataKuliah->sks ?? 0;
                });
            }
            
            // Hitung IPK
            $nilaiData = Nilai::with(['krs.kelas.mataKuliah'])
                ->whereHas('krs', function($q) use ($user) {
                    $q->where('mahasiswa_id', $user->mahasiswa_id);
                })
                ->get();
            
            $totalBobotXSks = 0;
            $totalSks = 0;
            
            $gradePoints = [
                'A' => 4.0,
                'A-' => 3.7,
                'B+' => 3.3,
                'B' => 3.0,
                'B-' => 2.7,
                'C+' => 2.3,
                'C' => 2.0,
                'D' => 1.0,
                'E' => 0.0,
            ];
            
            foreach ($nilaiData as $nilai) {
                if (isset($gradePoints[$nilai->nilai_huruf])) {
                    $sks = $nilai->krs->kelas->mataKuliah->sks ?? 0;
                    $bobot = $gradePoints[$nilai->nilai_huruf];
                    $totalBobotXSks += ($bobot * $sks);
                    $totalSks += $sks;
                }
            }
            
            $ipk = $totalSks > 0 ? round($totalBobotXSks / $totalSks, 2) : 0;
            
            $data = [
                'mahasiswa' => $mahasiswa,
                'semester_aktif' => $semesterAktif,
                'total_sks_lulus' => $totalSksLulus,
                'total_sks_semester_ini' => $totalSksSemesterIni,
                'ipk' => $ipk,
                'krs_aktif' => $krsAktif,
                'total_mata_kuliah_semester_ini' => $krsAktif->count(),
            ];
            
            return view('dashboard-mahasiswa', $data);
        }
        
        // Dashboard untuk Admin/Dosen
        $data = [
            'total_mahasiswa' => \App\Models\Mahasiswa::count(),
            'total_dosen' => \App\Models\Dosen::count(),
            'total_fakultas' => \App\Models\Fakultas::count(),
            'total_prodi' => \App\Models\ProgramStudi::count(),
            'total_mata_kuliah' => \App\Models\MataKuliah::count(),
            'mahasiswa_aktif' => \App\Models\Mahasiswa::where('status', 'Aktif')->count(),
        ];

        return view('dashboard', $data);
    }
}

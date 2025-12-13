<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa;
use App\Models\Krs;
use App\Models\Nilai;
use App\Models\Semester;
use App\Support\TabularExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
        
        // Dashboard untuk Admin Universitas
        if ($user->hasRole('admin_universitas')) {
            $data = [
                'total_fakultas' => \App\Models\Fakultas::count(),
                'total_prodi' => \App\Models\ProgramStudi::count(),
                'total_mahasiswa' => \App\Models\Mahasiswa::count(),
                'total_dosen' => \App\Models\Dosen::count(),
                'total_mata_kuliah' => \App\Models\MataKuliah::count(),
                'mahasiswa_aktif' => \App\Models\Mahasiswa::where('status', 'Aktif')->count(),
                'mahasiswa_baru' => \App\Models\Mahasiswa::whereYear('created_at', date('Y'))->count(),
                'pendaftar_baru' => \App\Models\PendaftaranMahasiswa::where('status', 'Pending')->count(),
            ];
            
            return view('dashboard-admin-universitas', $data);
        }
        
        // Dashboard untuk Admin Fakultas
        if ($user->hasRole('admin_fakultas')) {
            $fakultasId = $user->fakultas_id;
            
            $data = [
                'fakultas' => \App\Models\Fakultas::find($fakultasId),
                'total_prodi' => \App\Models\ProgramStudi::where('fakultas_id', $fakultasId)->count(),
                'total_mahasiswa' => \App\Models\Mahasiswa::whereHas('programStudi', function($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->count(),
                'total_dosen' => \App\Models\Dosen::where('fakultas_id', $fakultasId)->count(),
                'mahasiswa_aktif' => \App\Models\Mahasiswa::whereHas('programStudi', function($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->where('status', 'Aktif')->count(),
                'total_mata_kuliah' => \App\Models\MataKuliah::whereHas('programStudi', function($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->count(),
                'mahasiswa_baru' => \App\Models\Mahasiswa::whereHas('programStudi', function($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->whereYear('created_at', date('Y'))->count(),
            ];
            
            return view('dashboard-admin-fakultas', $data);
        }
        
        // Dashboard untuk Admin Prodi
        if ($user->hasRole('admin_prodi')) {
            $prodiId = $user->program_studi_id;
            
            $data = [
                'prodi' => \App\Models\ProgramStudi::with('fakultas')->find($prodiId),
                'total_mahasiswa' => \App\Models\Mahasiswa::where('program_studi_id', $prodiId)->count(),
                'mahasiswa_aktif' => \App\Models\Mahasiswa::where('program_studi_id', $prodiId)
                    ->where('status', 'Aktif')->count(),
                'total_dosen' => \App\Models\Dosen::where('program_studi_id', $prodiId)->count(),
                'total_mata_kuliah' => \App\Models\MataKuliah::where('program_studi_id', $prodiId)->count(),
                'mahasiswa_baru' => \App\Models\Mahasiswa::where('program_studi_id', $prodiId)
                    ->whereYear('created_at', date('Y'))->count(),
                'mahasiswa_per_semester' => \App\Models\Mahasiswa::where('program_studi_id', $prodiId)
                    ->where('status', 'Aktif')
                    ->selectRaw('semester, COUNT(*) as jumlah')
                    ->groupBy('semester')
                    ->orderBy('semester')
                    ->get(),
            ];
            
            return view('dashboard-admin-prodi', $data);
        }
        
        // Dashboard untuk Dosen
        if ($user->hasRole('dosen')) {
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            
            if (!$dosen) {
                return redirect()->route('logout')
                    ->with('error', 'Data dosen tidak ditemukan. Silakan hubungi administrator.');
            }
            
            $semesterAktif = Semester::where('is_active', true)->first();
            
            // Jadwal mengajar semester ini
            $jadwalMengajar = collect([]);
            if ($semesterAktif) {
                $parts = explode(' ', $semesterAktif->nama_semester);
                $tahunAjaran = $parts[0] ?? '';
                $semesterType = $parts[1] ?? '';
                
                $jadwalMengajar = \App\Models\JadwalKuliah::with(['mataKuliah', 'kelas', 'ruang'])
                    ->where('dosen_id', $dosen->id)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semesterType)
                    ->get();
            }
            
            $data = [
                'dosen' => $dosen,
                'semester_aktif' => $semesterAktif,
                'total_jadwal_mengajar' => $jadwalMengajar->count(),
                'jadwal_mengajar' => $jadwalMengajar,
                'total_mahasiswa_diampu' => \App\Models\Krs::whereHas('kelas.jadwalKuliah', function($q) use ($dosen) {
                    $q->where('dosen_id', $dosen->id);
                })->where('status', 'Disetujui')->distinct('mahasiswa_id')->count('mahasiswa_id'),
                'total_mata_kuliah' => $jadwalMengajar->unique('mata_kuliah_id')->count(),
            ];
            
            return view('dashboard-dosen', $data);
        }

        // Dashboard untuk Super Admin (default)
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

    public function exportExcel(Request $request)
    {
        [$filename, $headings, $rows] = $this->buildExportData();
        $html = TabularExport::htmlTable($headings, $rows);
        return TabularExport::excelResponse($filename . '.xls', $html);
    }

    public function exportPdf(Request $request)
    {
        [$filename, $headings, $rows, $orientation] = $this->buildExportData(includeOrientation: true);
        $html = TabularExport::htmlTable($headings, $rows);

        $pdf = Pdf::loadHTML($html);
        if ($orientation === 'landscape') {
            $pdf->setPaper('A4', 'landscape');
        }

        return $pdf->download($filename . '.pdf');
    }

    /**
     * @return array{0:string,1:array<int,string>,2:iterable<array<int,mixed>>,3?:'portrait'|'landscape'}
     */
    private function buildExportData(bool $includeOrientation = false): array
    {
        $user = Auth::user();

        // Mahasiswa: export KRS semester aktif (jika ada)
        if ($user->hasRole('mahasiswa')) {
            $mahasiswa = Mahasiswa::with(['programStudi.fakultas'])
                ->where('id', $user->mahasiswa_id)
                ->first();

            if (!$mahasiswa) {
                abort(404, 'Data mahasiswa tidak ditemukan.');
            }

            $semesterAktif = Semester::where('is_active', true)->first();
            $krsAktif = collect([]);
            $tahunAjaran = '';
            $semesterType = '';

            if ($semesterAktif) {
                $parts = explode(' ', $semesterAktif->nama_semester);
                $tahunAjaran = $parts[0] ?? '';
                $semesterType = $parts[1] ?? '';

                $krsAktif = Krs::with(['kelas.mataKuliah', 'kelas.dosen'])
                    ->where('mahasiswa_id', $user->mahasiswa_id)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semesterType)
                    ->where('status', 'Disetujui')
                    ->get();
            }

            $headings = ['No', 'Mata Kuliah', 'Kode', 'Kelas', 'Dosen', 'SKS', 'Tahun Ajaran', 'Semester'];
            $rows = $krsAktif->map(function ($krs, int $index) use ($tahunAjaran, $semesterType) {
                $mk = $krs->kelas->mataKuliah ?? null;
                $namaMk = $mk->nama_mk ?? ($mk->nama_mata_kuliah ?? ($mk->nama ?? '-'));
                $kodeMk = $mk->kode_mk ?? ($mk->kode_mata_kuliah ?? ($mk->kode ?? '-'));

                return [
                    $index + 1,
                    $namaMk,
                    $kodeMk,
                    $krs->kelas->nama_kelas ?? '-',
                    $krs->kelas->dosen->nama_dosen ?? ($krs->kelas->dosen->nama ?? '-'),
                    (string) ($mk->sks ?? 0),
                    $tahunAjaran ?: '-',
                    $semesterType ?: '-',
                ];
            });

            $result = ['dashboard_mahasiswa_krs', $headings, $rows];
            if ($includeOrientation) {
                $result[] = 'landscape';
            }
            return $result;
        }

        // Dosen: export jadwal mengajar semester aktif
        if ($user->hasRole('dosen')) {
            $dosen = \App\Models\Dosen::where('user_id', $user->id)->first();
            if (!$dosen) {
                abort(404, 'Data dosen tidak ditemukan.');
            }

            $semesterAktif = Semester::where('is_active', true)->first();
            $jadwalMengajar = collect([]);

            if ($semesterAktif) {
                $parts = explode(' ', $semesterAktif->nama_semester);
                $tahunAjaran = $parts[0] ?? '';
                $semesterType = $parts[1] ?? '';

                $jadwalMengajar = \App\Models\JadwalKuliah::with(['mataKuliah', 'kelas', 'ruang'])
                    ->where('dosen_id', $dosen->id)
                    ->where('tahun_ajaran', $tahunAjaran)
                    ->where('semester', $semesterType)
                    ->orderBy('hari')
                    ->orderBy('jam_mulai')
                    ->get();
            }

            $headings = ['No', 'Hari', 'Jam Mulai', 'Jam Selesai', 'Mata Kuliah', 'Kode', 'Kelas', 'Ruang', 'SKS'];
            $rows = $jadwalMengajar->map(function ($jadwal, int $index) {
                $mk = $jadwal->mataKuliah ?? null;
                $namaMk = $mk->nama_mk ?? ($mk->nama_mata_kuliah ?? ($mk->nama ?? '-'));
                $kodeMk = $mk->kode_mk ?? ($mk->kode_mata_kuliah ?? ($mk->kode ?? '-'));

                return [
                    $index + 1,
                    $jadwal->hari ?? '-',
                    $jadwal->jam_mulai ?? '-',
                    $jadwal->jam_selesai ?? '-',
                    $namaMk,
                    $kodeMk,
                    $jadwal->kelas->nama_kelas ?? '-',
                    $jadwal->ruang->nama_ruang ?? ($jadwal->ruang->kode_ruang ?? '-'),
                    (string) ($mk->sks ?? 0),
                ];
            });

            $result = ['dashboard_dosen_jadwal', $headings, $rows];
            if ($includeOrientation) {
                $result[] = 'landscape';
            }
            return $result;
        }

        // Admins & default: export summary metrics
        $headings = ['Metric', 'Value'];

        if ($user->hasRole('admin_universitas')) {
            $rows = [
                ['Total Fakultas', (string) \App\Models\Fakultas::count()],
                ['Total Program Studi', (string) \App\Models\ProgramStudi::count()],
                ['Total Mahasiswa', (string) \App\Models\Mahasiswa::count()],
                ['Total Dosen', (string) \App\Models\Dosen::count()],
                ['Total Mata Kuliah', (string) \App\Models\MataKuliah::count()],
                ['Mahasiswa Aktif', (string) \App\Models\Mahasiswa::where('status', 'Aktif')->count()],
                ['Mahasiswa Baru (Tahun Ini)', (string) \App\Models\Mahasiswa::whereYear('created_at', date('Y'))->count()],
                ['Pendaftar Baru (Pending)', (string) \App\Models\PendaftaranMahasiswa::where('status', 'Pending')->count()],
            ];
            $result = ['dashboard_admin_universitas', $headings, $rows];
            if ($includeOrientation) {
                $result[] = 'portrait';
            }
            return $result;
        }

        if ($user->hasRole('admin_fakultas')) {
            $fakultasId = $user->fakultas_id;
            $rows = [
                ['Total Program Studi', (string) \App\Models\ProgramStudi::where('fakultas_id', $fakultasId)->count()],
                ['Total Mahasiswa', (string) \App\Models\Mahasiswa::whereHas('programStudi', function ($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->count()],
                ['Total Dosen', (string) \App\Models\Dosen::where('fakultas_id', $fakultasId)->count()],
                ['Mahasiswa Aktif', (string) \App\Models\Mahasiswa::whereHas('programStudi', function ($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->where('status', 'Aktif')->count()],
                ['Total Mata Kuliah', (string) \App\Models\MataKuliah::whereHas('programStudi', function ($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->count()],
                ['Mahasiswa Baru (Tahun Ini)', (string) \App\Models\Mahasiswa::whereHas('programStudi', function ($q) use ($fakultasId) {
                    $q->where('fakultas_id', $fakultasId);
                })->whereYear('created_at', date('Y'))->count()],
            ];
            $result = ['dashboard_admin_fakultas', $headings, $rows];
            if ($includeOrientation) {
                $result[] = 'portrait';
            }
            return $result;
        }

        if ($user->hasRole('admin_prodi')) {
            $prodiId = $user->program_studi_id;
            $rows = [
                ['Total Mahasiswa', (string) \App\Models\Mahasiswa::where('program_studi_id', $prodiId)->count()],
                ['Mahasiswa Aktif', (string) \App\Models\Mahasiswa::where('program_studi_id', $prodiId)->where('status', 'Aktif')->count()],
                ['Total Dosen', (string) \App\Models\Dosen::where('program_studi_id', $prodiId)->count()],
                ['Total Mata Kuliah', (string) \App\Models\MataKuliah::where('program_studi_id', $prodiId)->count()],
                ['Mahasiswa Baru (Tahun Ini)', (string) \App\Models\Mahasiswa::where('program_studi_id', $prodiId)->whereYear('created_at', date('Y'))->count()],
            ];
            $result = ['dashboard_admin_prodi', $headings, $rows];
            if ($includeOrientation) {
                $result[] = 'portrait';
            }
            return $result;
        }

        // Super admin / fallback
        $rows = [
            ['Total Mahasiswa', (string) \App\Models\Mahasiswa::count()],
            ['Total Dosen', (string) \App\Models\Dosen::count()],
            ['Total Fakultas', (string) \App\Models\Fakultas::count()],
            ['Total Program Studi', (string) \App\Models\ProgramStudi::count()],
            ['Total Mata Kuliah', (string) \App\Models\MataKuliah::count()],
            ['Mahasiswa Aktif', (string) \App\Models\Mahasiswa::where('status', 'Aktif')->count()],
        ];

        $result = ['dashboard', $headings, $rows];
        if ($includeOrientation) {
            $result[] = 'portrait';
        }
        return $result;
    }
}

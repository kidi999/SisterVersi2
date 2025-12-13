<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\JadwalKuliah;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\Ruang;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalKuliahExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_jadwal_kuliah_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        $matkul = MataKuliah::create([
            'level_matkul' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'kode_mk' => 'IF101',
            'nama_mk' => 'Algoritma',
            'sks' => 3,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $dosen = Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011010',
            'nidn' => '1234500001',
            'nama_dosen' => 'Dosen Jadwal',
            'jenis_kelamin' => 'L',
            'email' => 'dosen.jadwal@example.test',
            'status' => 'Aktif',
        ]);

        $kelas = Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'A',
            'nama_kelas' => 'Kelas A',
            'tahun_ajaran' => '2025',
            'semester' => 'Ganjil',
            'kapasitas' => 40,
            'terisi' => 0,
        ]);

        $ruang = Ruang::create([
            'kode_ruang' => 'R001',
            'nama_ruang' => 'Ruang A',
            'kapasitas' => 40,
            'jenis_ruang' => 'Kelas',
            'tingkat_kepemilikan' => 'Universitas',
            'status' => 'Aktif',
        ]);

        $tahunAkademik = TahunAkademik::create([
            'kode' => '2025/2026',
            'nama' => 'Tahun Akademik 2025/2026',
            'tahun_mulai' => 2025,
            'tahun_selesai' => 2026,
            'tanggal_mulai' => '2025-08-01',
            'tanggal_selesai' => '2026-07-31',
            'is_active' => true,
        ]);

        $semester = Semester::create([
            'tahun_akademik_id' => $tahunAkademik->id,
            'nama_semester' => 'Ganjil',
            'nomor_semester' => 1,
            'tanggal_mulai' => '2025-08-01',
            'tanggal_selesai' => '2025-12-31',
            'is_active' => true,
        ]);

        JadwalKuliah::create([
            'kelas_id' => $kelas->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester_id' => $semester->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'ruang_id' => $ruang->id,
        ]);

        $response = $this->actingAs($admin)->get(route('jadwal-kuliah.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Algoritma');
    }

    public function test_jadwal_kuliah_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FE',
            'nama_fakultas' => 'Fakultas Ekonomi',
            'singkatan' => 'FE',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'MN',
            'nama_prodi' => 'Manajemen',
            'jenjang' => 'S1',
        ]);

        $matkul = MataKuliah::create([
            'level_matkul' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'kode_mk' => 'MN101',
            'nama_mk' => 'Pengantar Manajemen',
            'sks' => 2,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $dosen = Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011011',
            'nama_dosen' => 'Dosen PDF',
            'jenis_kelamin' => 'P',
            'email' => 'dosen.pdf.jadwal@example.test',
            'status' => 'Aktif',
        ]);

        $kelas = Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'B',
            'nama_kelas' => 'Kelas B',
            'tahun_ajaran' => '2025',
            'semester' => 'Genap',
            'kapasitas' => 30,
            'terisi' => 10,
        ]);

        $ruang = Ruang::create([
            'kode_ruang' => 'R002',
            'nama_ruang' => 'Ruang B',
            'kapasitas' => 20,
            'jenis_ruang' => 'Lab',
            'tingkat_kepemilikan' => 'Universitas',
            'status' => 'Aktif',
        ]);

        $tahunAkademik = TahunAkademik::create([
            'kode' => '2024/2025',
            'nama' => 'Tahun Akademik 2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2025-07-31',
            'is_active' => true,
        ]);

        $semester = Semester::create([
            'tahun_akademik_id' => $tahunAkademik->id,
            'nama_semester' => 'Genap',
            'nomor_semester' => 2,
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'is_active' => true,
        ]);

        JadwalKuliah::create([
            'kelas_id' => $kelas->id,
            'tahun_akademik_id' => $tahunAkademik->id,
            'semester_id' => $semester->id,
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'ruang_id' => $ruang->id,
        ]);

        $response = $this->actingAs($admin)->get(route('jadwal-kuliah.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NilaiExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_nilai_export_excel_returns_xls(): void
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
            'kode_mk' => 'IF301',
            'nama_mk' => 'Jaringan Komputer',
            'sks' => 3,
            'semester' => 5,
            'jenis' => 'Wajib',
        ]);

        $dosen = Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011030',
            'nama_dosen' => 'Dosen Nilai',
            'jenis_kelamin' => 'P',
            'email' => 'dosen.nilai@example.test',
            'status' => 'Aktif',
        ]);

        $kelas = Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'N1',
            'nama_kelas' => 'Kelas Nilai 1',
            'tahun_ajaran' => '2024',
            'semester' => 'Ganjil',
            'kapasitas' => 40,
            'terisi' => 1,
        ]);

        $mahasiswa = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2201010',
            'nama_mahasiswa' => 'Mahasiswa Nilai',
            'jenis_kelamin' => 'L',
            'email' => 'mhs.nilai@example.test',
            'tahun_masuk' => '2022',
            'status' => 'Aktif',
        ]);

        $krs = Krs::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran' => '2024',
            'semester' => 'Ganjil',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => now(),
        ]);

        Nilai::create([
            'krs_id' => $krs->id,
            'nilai_tugas' => 80,
            'nilai_uts' => 75,
            'nilai_uas' => 85,
            'nilai_akhir' => 80,
            'nilai_huruf' => 'A-',
            'bobot' => 3.7,
        ]);

        $response = $this->actingAs($admin)->get(route('nilai.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Mahasiswa Nilai');
    }

    public function test_nilai_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $response = $this->actingAs($admin)->get(route('nilai.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

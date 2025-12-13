<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KrsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_krs_export_excel_returns_xls(): void
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
            'kode_mk' => 'IF201',
            'nama_mk' => 'Basis Data',
            'sks' => 3,
            'semester' => 3,
            'jenis' => 'Wajib',
        ]);

        $dosen = Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011020',
            'nama_dosen' => 'Dosen KRS',
            'jenis_kelamin' => 'L',
            'email' => 'dosen.krs@example.test',
            'status' => 'Aktif',
        ]);

        $kelas = Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'KRS1',
            'nama_kelas' => 'Kelas KRS 1',
            'tahun_ajaran' => '2024',
            'semester' => 'Ganjil',
            'kapasitas' => 40,
            'terisi' => 1,
        ]);

        $mahasiswa = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2201009',
            'nama_mahasiswa' => 'Mahasiswa KRS',
            'jenis_kelamin' => 'L',
            'email' => 'mhs.krs@example.test',
            'tahun_masuk' => '2022',
            'status' => 'Aktif',
        ]);

        Krs::create([
            'mahasiswa_id' => $mahasiswa->id,
            'kelas_id' => $kelas->id,
            'tahun_ajaran' => '2024',
            'semester' => 'Ganjil',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('krs.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Mahasiswa KRS');
    }

    public function test_krs_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $response = $this->actingAs($admin)->get(route('krs.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

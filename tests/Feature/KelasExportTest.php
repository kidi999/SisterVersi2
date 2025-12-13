<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KelasExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_kelas_export_excel_returns_xls(): void
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
            'nama_dosen' => 'Dosen Kelas',
            'jenis_kelamin' => 'L',
            'email' => 'dosen.kelas@example.test',
            'status' => 'Aktif',
        ]);

        Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'A',
            'nama_kelas' => 'Kelas A',
            'tahun_ajaran' => '2024',
            'semester' => 'Ganjil',
            'kapasitas' => 40,
            'terisi' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('kelas.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Kelas A');
    }

    public function test_kelas_export_pdf_returns_pdf(): void
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
            'email' => 'dosen.pdf@example.test',
            'status' => 'Aktif',
        ]);

        Kelas::create([
            'mata_kuliah_id' => $matkul->id,
            'dosen_id' => $dosen->id,
            'kode_kelas' => 'B',
            'nama_kelas' => 'Kelas B',
            'tahun_ajaran' => '2024',
            'semester' => 'Genap',
            'kapasitas' => 30,
            'terisi' => 10,
        ]);

        $response = $this->actingAs($admin)->get(route('kelas.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

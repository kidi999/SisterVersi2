<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MahasiswaExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_export_excel_returns_xls(): void
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

        Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2201001',
            'nama_mahasiswa' => 'Mahasiswa A',
            'jenis_kelamin' => 'L',
            'email' => 'mhs.a@example.test',
            'tahun_masuk' => '2022',
            'semester' => 5,
            'ipk' => 3.45,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('mahasiswa.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Mahasiswa A');
    }

    public function test_mahasiswa_export_pdf_returns_pdf(): void
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

        Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2202001',
            'nama_mahasiswa' => 'Mahasiswa B',
            'jenis_kelamin' => 'P',
            'email' => 'mhs.b@example.test',
            'tahun_masuk' => '2022',
        ]);

        $response = $this->actingAs($admin)->get(route('mahasiswa.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

<?php

namespace Tests\Feature;

use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_export_excel_returns_xls(): void
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

        Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011001',
            'nidn' => '1234567890',
            'nama_dosen' => 'Dosen A',
            'jenis_kelamin' => 'L',
            'email' => 'dosen.a@example.test',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('dosen.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Dosen A');
    }

    public function test_dosen_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FH',
            'nama_fakultas' => 'Fakultas Hukum',
            'singkatan' => 'FH',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'HK',
            'nama_prodi' => 'Ilmu Hukum',
            'jenjang' => 'S1',
        ]);

        Dosen::create([
            'level_dosen' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'nip' => '198701012022011002',
            'nama_dosen' => 'Dosen B',
            'jenis_kelamin' => 'P',
            'email' => 'dosen.b@example.test',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('dosen.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

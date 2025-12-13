<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgramStudiExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_program_studi_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. A',
            'akreditasi' => 'A',
        ]);

        $response = $this->actingAs($admin)->get(route('program-studi.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Teknik Informatika');
    }

    public function test_program_studi_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FE',
            'nama_fakultas' => 'Fakultas Ekonomi',
            'singkatan' => 'FE',
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'AK',
            'nama_prodi' => 'Akuntansi',
            'jenjang' => 'S1',
        ]);

        $response = $this->actingAs($admin)->get(route('program-studi.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

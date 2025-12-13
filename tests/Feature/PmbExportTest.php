<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Fakultas;
use App\Models\ProgramStudi;

class PmbExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pmb_export_excel_returns_excel_response(): void
    {
        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FK01',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI01',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kaprodi' => null,
            'akreditasi' => 'A',
        ]);

        $response = $this->get(route('pmb.exportExcel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains((string) $response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Teknik Informatika');
    }

    public function test_pmb_export_pdf_returns_pdf_response(): void
    {
        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FK01',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI01',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kaprodi' => null,
            'akreditasi' => 'A',
        ]);

        $response = $this->get(route('pmb.exportPdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

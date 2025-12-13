<?php

namespace Tests\Feature;

use App\Models\AkreditasiUniversitas;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AkreditasiUniversitasExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_akreditasi_universitas_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $university = University::create([
            'kode' => 'UNIV01',
            'nama' => 'Universitas Export',
            'jenis' => 'Negeri',
            'status' => 'Aktif',
        ]);

        AkreditasiUniversitas::create([
            'university_id' => $university->id,
            'lembaga_akreditasi' => 'BAN-PT',
            'nomor_sk' => 'SK-001',
            'tanggal_sk' => '2025-01-01',
            'peringkat' => 'Unggul',
            'tahun_akreditasi' => 2025,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('akreditasi-universitas.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Universitas Export');
    }

    public function test_akreditasi_universitas_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $university = University::create([
            'kode' => 'UNIV02',
            'nama' => 'Universitas PDF',
            'jenis' => 'Swasta',
            'status' => 'Aktif',
        ]);

        AkreditasiUniversitas::create([
            'university_id' => $university->id,
            'lembaga_akreditasi' => 'BAN-PT',
            'nomor_sk' => 'SK-002',
            'tanggal_sk' => '2025-02-02',
            'peringkat' => 'A',
            'tahun_akreditasi' => 2025,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('akreditasi-universitas.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

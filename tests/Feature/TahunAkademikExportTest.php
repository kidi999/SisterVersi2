<?php

namespace Tests\Feature;

use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahunAkademikExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_tahun_akademik_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        TahunAkademik::create([
            'kode' => '2024/2025',
            'nama' => 'Tahun Akademik 2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2025-07-31',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('tahun-akademik.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('2024/2025');
    }

    public function test_tahun_akademik_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        TahunAkademik::create([
            'kode' => '2023/2024',
            'nama' => 'Tahun Akademik 2023/2024',
            'tahun_mulai' => 2023,
            'tahun_selesai' => 2024,
            'tanggal_mulai' => '2023-08-01',
            'tanggal_selesai' => '2024-07-31',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('tahun-akademik.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

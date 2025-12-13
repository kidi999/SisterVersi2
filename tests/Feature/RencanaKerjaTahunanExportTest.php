<?php

namespace Tests\Feature;

use App\Models\RencanaKerjaTahunan;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RencanaKerjaTahunanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_rencana_kerja_tahunan_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $university = University::factory()->create([
            'nama' => 'Universitas RKT',
            'kode' => 'URKT',
            'status' => 'Aktif',
            'jenis' => 'Negeri',
        ]);

        RencanaKerjaTahunan::create([
            'kode_rkt' => 'RKTU/2025/0001',
            'judul_rkt' => 'RKT Test',
            'tahun' => 2025,
            'level' => 'Universitas',
            'university_id' => $university->id,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(10)->toDateString(),
            'anggaran' => 5000000,
            'status' => RencanaKerjaTahunan::STATUS_DRAFT,
        ]);

        $response = $this->actingAs($admin)->get(route('rencana-kerja-tahunan.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('RKT Test');
    }

    public function test_rencana_kerja_tahunan_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $response = $this->actingAs($admin)->get(route('rencana-kerja-tahunan.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

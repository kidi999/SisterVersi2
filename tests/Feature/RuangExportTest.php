<?php

namespace Tests\Feature;

use App\Models\Ruang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuangExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_ruang_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        Ruang::create([
            'kode_ruang' => 'R001',
            'nama_ruang' => 'Ruang A',
            'gedung' => 'Gedung 1',
            'lantai' => '1',
            'kapasitas' => 40,
            'jenis_ruang' => 'Kelas',
            'tingkat_kepemilikan' => 'Universitas',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('ruang.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Ruang A');
    }

    public function test_ruang_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        Ruang::create([
            'kode_ruang' => 'R002',
            'nama_ruang' => 'Ruang B',
            'kapasitas' => 20,
            'jenis_ruang' => 'Lab',
            'tingkat_kepemilikan' => 'Universitas',
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('ruang.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

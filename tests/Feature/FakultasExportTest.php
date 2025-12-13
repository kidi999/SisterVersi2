<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FakultasExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_fakultas_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
        ]);

        $response = $this->actingAs($admin)->get(route('fakultas.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Fakultas Teknik');
    }

    public function test_fakultas_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        Fakultas::create([
            'kode_fakultas' => 'FE',
            'nama_fakultas' => 'Fakultas Ekonomi',
            'singkatan' => 'FE',
        ]);

        $response = $this->actingAs($admin)->get(route('fakultas.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

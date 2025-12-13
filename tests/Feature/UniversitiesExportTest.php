<?php

namespace Tests\Feature;

use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversitiesExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_universities_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        University::factory()->create([
            'nama' => 'Universitas Contoh',
            'kode' => 'UC',
            'status' => 'Aktif',
            'jenis' => 'Negeri',
        ]);

        $response = $this->actingAs($admin)->get(route('universities.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Universitas Contoh');
    }

    public function test_universities_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        University::factory()->create([
            'nama' => 'Universitas PDF',
            'kode' => 'UP',
            'status' => 'Aktif',
            'jenis' => 'Swasta',
        ]);

        $response = $this->actingAs($admin)->get(route('universities.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DashboardExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_export_excel_returns_excel_response(): void
    {
        $user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard.exportExcel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains((string) $response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Total Mahasiswa');
    }

    public function test_dashboard_export_pdf_returns_pdf_response(): void
    {
        $user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard.exportPdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

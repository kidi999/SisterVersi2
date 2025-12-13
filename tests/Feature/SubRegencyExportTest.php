<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use App\Models\User;

class SubRegencyExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($this->user);
    }

    public function test_export_csv_returns_csv_response()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        SubRegency::factory()->count(3)->create(['regency_id' => $regency->id]);

        $response = $this->get(route('sub-regency.exportCsv'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/csv'));
        // Ambil isi stream CSV
        ob_start();
        $response->sendContent();
        $csv = ob_get_clean();
        $this->assertStringContainsString('Nama Kecamatan', $csv);
    }

    public function test_export_pdf_returns_pdf_response()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        SubRegency::factory()->count(2)->create(['regency_id' => $regency->id]);

        $response = $this->get(route('sub-regency.exportPdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_index_pagination_works()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        SubRegency::factory()->count(15)->create(['regency_id' => $regency->id]);

        $response = $this->get(route('sub-regency.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Kecamatan');
        $response->assertSee('pagination');
    }
}

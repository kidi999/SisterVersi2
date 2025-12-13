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

    private int $initialObLevel = 0;

    private function getStreamedContent($response): string
    {
        if (method_exists($response, 'streamedContent')) {
            return (string) $response->streamedContent();
        }

        ob_start();
        $response->sendContent();
        return (string) (ob_get_clean() ?: '');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initialObLevel = ob_get_level();
        $this->user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialObLevel) {
            ob_end_clean();
        }
        parent::tearDown();
    }

    public function test_export_csv_returns_csv_response()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        SubRegency::factory()->count(3)->create(['regency_id' => $regency->id]);

        $response = $this->get(route('sub-regency.exportCsv'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/csv'));
        $csv = $this->getStreamedContent($response);
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

    public function test_export_excel_returns_excel_response()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        SubRegency::factory()->count(3)->create(['regency_id' => $regency->id]);

        $response = $this->get(route('sub-regency.exportExcel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Nama Kecamatan');
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

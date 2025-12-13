<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Village;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;
use App\Models\User;

class VillageExportTest extends TestCase
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
        $subRegency = SubRegency::factory()->create(['regency_id' => $regency->id]);
        Village::factory()->count(3)->create(['sub_regency_id' => $subRegency->id]);

        $response = $this->get(route('village.exportCsv'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'text/csv'));
        ob_start();
        $response->sendContent();
        $csv = ob_get_clean();
        $this->assertStringContainsString('Nama Desa/Kelurahan', $csv);
    }

    public function test_export_pdf_returns_pdf_response()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        $subRegency = SubRegency::factory()->create(['regency_id' => $regency->id]);
        Village::factory()->count(2)->create(['sub_regency_id' => $subRegency->id]);

        $response = $this->get(route('village.exportPdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_index_pagination_and_search_works()
    {
        $province = Province::factory()->create();
        $regency = Regency::factory()->create(['province_id' => $province->id]);
        $subRegency = SubRegency::factory()->create(['regency_id' => $regency->id]);
        Village::factory()->count(30)->create(['sub_regency_id' => $subRegency->id]);

        $response = $this->get(route('village.index'));
        $response->assertStatus(200);
        $response->assertSee('Data Desa/Kelurahan');
        $response->assertSee('pagination');

        // Test search
        $village = Village::first();
        $response = $this->get(route('village.index', ['search' => $village->name]));
        $response->assertStatus(200);
        $response->assertSee($village->name);
    }
}

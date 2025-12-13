<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Province;
use App\Models\User;
use App\Models\Role;

class ProvinceExportCsvTest extends TestCase
{
    use RefreshDatabase;

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
        Province::factory()->count(5)->create();
        $role = Role::factory()->create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
        ]);
        $this->user = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_export_csv_provinces_success()
    {
        $this->actingAs($this->user);
        $response = $this->get('/provinsi-export-csv');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
        $response->assertHeader('content-disposition', 'attachment; filename="provinsi.csv"');
        $csv = $this->getStreamedContent($response);
        $this->assertStringContainsString('ID,Kode', $csv);
        $this->assertStringContainsString('Nama Provinsi', $csv);
    }
}

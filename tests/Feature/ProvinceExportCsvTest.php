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
        $response->assertHeader('content-type', 'text/csv');
        $response->assertHeader('content-disposition', 'attachment; filename="provinsi.csv"');
        $this->assertStringContainsString('ID,Kode,Nama Provinsi', $response->getContent());
    }
}

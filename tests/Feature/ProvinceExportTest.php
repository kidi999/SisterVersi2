<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\Province;
use App\Models\User;

class ProvinceExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed database with provinces
        Province::factory()->count(5)->create();
        // Seed super_admin role
        $role = \App\Models\Role::factory()->create([
            'name' => 'super_admin',
            'display_name' => 'Super Admin',
        ]);
        // Set default user factory to super_admin
        \App\Models\User::factory()->recycle($role);
    }

    public function test_export_excel_provinces_success()
    {
        $user = User::factory()->create(['role_id' => \App\Models\Role::where('name', 'super_admin')->first()->id]);
        $this->actingAs($user);
        $response = $this->get('/provinsi-export?type=excel');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=provinsi.xlsx');
    }

    public function test_export_pdf_provinces_success()
    {
        $user = User::factory()->create(['role_id' => \App\Models\Role::where('name', 'super_admin')->first()->id]);
        $this->actingAs($user);
        $response = $this->get('/provinsi-export?type=pdf');
        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=provinsi.pdf');
    }

    public function test_export_invalid_type_returns_error()
    {
        $user = User::factory()->create(['role_id' => \App\Models\Role::where('name', 'super_admin')->first()->id]);
        $this->actingAs($user);
        $response = $this->get('/provinsi-export?type=invalid');
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}

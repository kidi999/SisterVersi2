<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        User::factory()->withSuperAdminRole()->create([
            'name' => 'User Two',
            'email' => 'user.two@example.test',
        ]);

        $response = $this->actingAs($admin)->get(route('users.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('User Two');
    }

    public function test_users_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        User::factory()->withSuperAdminRole()->create([
            'name' => 'User PDF',
            'email' => 'user.pdf@example.test',
        ]);

        $response = $this->actingAs($admin)->get(route('users.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

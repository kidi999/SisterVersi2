<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\University;
use App\Models\User;

class UniversityProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->withSuperAdminRole()->create();
        $this->actingAs($this->user);
    }

    public function test_university_profile_page_shows_data()
    {
        $university = University::factory()->create([
            'status' => 'Aktif',
            'nama' => 'Universitas Contoh',
            'singkatan' => 'UC',
            'visi' => 'Menjadi universitas terbaik.',
            'misi' => 'Mencerdaskan kehidupan bangsa.',
        ]);

        $response = $this->get(route('university.profile'));
        $response->assertStatus(200);
        $response->assertSee('Universitas Contoh');
        $response->assertSee('UC');
        $response->assertSee('Menjadi universitas terbaik.');
        $response->assertSee('Mencerdaskan kehidupan bangsa.');
    }

    public function test_university_profile_404_if_not_found()
    {
        $response = $this->get(route('university.profile'));
        $response->assertStatus(404);
        $response->assertSee('Not Found');
    }
}

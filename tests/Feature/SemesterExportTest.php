<?php

namespace Tests\Feature;

use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemesterExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_semester_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $tahunAkademik = TahunAkademik::create([
            'kode' => '2024/2025',
            'nama' => 'Tahun Akademik 2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2025-07-31',
            'is_active' => false,
        ]);

        Semester::create([
            'tahun_akademik_id' => $tahunAkademik->id,
            'program_studi_id' => null,
            'nama_semester' => 'Ganjil 2024',
            'nomor_semester' => 1,
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2024-12-31',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('semester.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Ganjil 2024');
    }

    public function test_semester_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $tahunAkademik = TahunAkademik::create([
            'kode' => '2024/2025',
            'nama' => 'Tahun Akademik 2024/2025',
            'tahun_mulai' => 2024,
            'tahun_selesai' => 2025,
            'tanggal_mulai' => '2024-08-01',
            'tanggal_selesai' => '2025-07-31',
            'is_active' => false,
        ]);

        Semester::create([
            'tahun_akademik_id' => $tahunAkademik->id,
            'program_studi_id' => null,
            'nama_semester' => 'Genap 2024',
            'nomor_semester' => 2,
            'tanggal_mulai' => '2025-01-01',
            'tanggal_selesai' => '2025-06-30',
            'is_active' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('semester.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

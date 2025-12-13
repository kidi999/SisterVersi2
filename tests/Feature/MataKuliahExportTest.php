<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\MataKuliah;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MataKuliahExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_mata_kuliah_export_excel_returns_xls(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
        ]);

        MataKuliah::create([
            'level_matkul' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'kode_mk' => 'IF101',
            'nama_mk' => 'Algoritma',
            'sks' => 3,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $response = $this->actingAs($admin)->get(route('mata-kuliah.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Algoritma');
    }

    public function test_mata_kuliah_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FK',
            'nama_fakultas' => 'Fakultas Kedokteran',
            'singkatan' => 'FK',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'KD',
            'nama_prodi' => 'Pendidikan Dokter',
            'jenjang' => 'S1',
        ]);

        MataKuliah::create([
            'level_matkul' => 'prodi',
            'fakultas_id' => $fakultas->id,
            'program_studi_id' => $prodi->id,
            'kode_mk' => 'KD101',
            'nama_mk' => 'Anatomi',
            'sks' => 4,
            'semester' => 1,
            'jenis' => 'Wajib',
        ]);

        $response = $this->actingAs($admin)->get(route('mata-kuliah.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

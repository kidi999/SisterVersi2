<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\PendaftaranMahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PendaftaranMahasiswaExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pendaftaran_mahasiswa_export_excel_returns_xls(): void
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

        PendaftaranMahasiswa::create([
            'tahun_akademik' => '2025/2026',
            'jalur_masuk' => 'Mandiri',
            'program_studi_id' => $prodi->id,
            'no_pendaftaran' => PendaftaranMahasiswa::generateNoPendaftaran('2025/2026', $prodi->id),
            'nama_lengkap' => 'Pendaftar A',
            'jenis_kelamin' => 'L',
            'email' => 'pendaftar.a@example.test',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)->get(route('pendaftaran-mahasiswa.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Pendaftar A');
    }

    public function test_pendaftaran_mahasiswa_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FE',
            'nama_fakultas' => 'Fakultas Ekonomi',
            'singkatan' => 'FE',
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'MN',
            'nama_prodi' => 'Manajemen',
            'jenjang' => 'S1',
        ]);

        PendaftaranMahasiswa::create([
            'tahun_akademik' => '2025/2026',
            'jalur_masuk' => 'SNBT',
            'program_studi_id' => $prodi->id,
            'no_pendaftaran' => PendaftaranMahasiswa::generateNoPendaftaran('2025/2026', $prodi->id),
            'nama_lengkap' => 'Pendaftar B',
            'jenis_kelamin' => 'P',
            'email' => 'pendaftar.b@example.test',
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($admin)->get(route('pendaftaran-mahasiswa.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

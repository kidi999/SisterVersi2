<?php

namespace Tests\Feature;

use App\Models\Fakultas;
use App\Models\JenisPembayaran;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\TagihanMahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanMahasiswaExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_tagihan_mahasiswa_export_excel_returns_xls(): void
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

        $mahasiswa = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => '2301001',
            'nama_mahasiswa' => 'Mahasiswa Tagihan',
            'jenis_kelamin' => 'L',
            'email' => 'tagihan.mhs@example.test',
            'tahun_masuk' => '2023',
            'semester' => 3,
            'ipk' => 3.00,
            'status' => 'Aktif',
        ]);

        $jenis = JenisPembayaran::create([
            'kode' => 'SPP',
            'nama' => 'SPP',
            'kategori' => 'Tetap',
            'is_wajib' => true,
            'is_active' => true,
            'urutan' => 1,
        ]);

        $ta = TahunAkademik::create([
            'kode' => 'TA2324',
            'nama' => '2023/2024',
            'tahun_mulai' => 2023,
            'tahun_selesai' => 2024,
            'tanggal_mulai' => '2023-08-01',
            'tanggal_selesai' => '2024-07-31',
            'is_active' => true,
        ]);

        $semester = Semester::create([
            'tahun_akademik_id' => $ta->id,
            'program_studi_id' => $prodi->id,
            'nama_semester' => Semester::SEMESTER_GANJIL,
            'nomor_semester' => 1,
            'tanggal_mulai' => '2023-08-01',
            'tanggal_selesai' => '2023-12-31',
            'is_active' => true,
        ]);

        TagihanMahasiswa::create([
            'mahasiswa_id' => $mahasiswa->id,
            'jenis_pembayaran_id' => $jenis->id,
            'tahun_akademik_id' => $ta->id,
            'semester_id' => $semester->id,
            'nomor_tagihan' => 'TGH/2025/01/00001',
            'jumlah_tagihan' => 1000000,
            'jumlah_dibayar' => 0,
            'sisa_tagihan' => 1000000,
            'tanggal_tagihan' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(30)->toDateString(),
            'status' => 'Belum Dibayar',
            'denda' => 0,
            'diskon' => 0,
        ]);

        $response = $this->actingAs($admin)->get(route('tagihan-mahasiswa.exportExcel'));

        $response->assertOk();
        $this->assertTrue(str_contains($response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Mahasiswa Tagihan');
    }

    public function test_tagihan_mahasiswa_export_pdf_returns_pdf(): void
    {
        $admin = User::factory()->withSuperAdminRole()->create();

        $response = $this->actingAs($admin)->get(route('tagihan-mahasiswa.exportPdf'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

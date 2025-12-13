<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use App\Models\Mahasiswa;
use App\Models\Role;
use App\Models\User;

class ProfilMahasiswaExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeMahasiswaUser(): User
    {
        $role = Role::firstOrCreate(
            ['name' => 'mahasiswa'],
            ['display_name' => 'Mahasiswa']
        );

        $fakultas = Fakultas::create([
            'kode_fakultas' => 'FK02',
            'nama_fakultas' => 'Fakultas Sains',
            'singkatan' => 'FS',
            'dekan' => null,
            'alamat' => null,
            'telepon' => null,
            'email' => null,
        ]);

        $prodi = ProgramStudi::create([
            'fakultas_id' => $fakultas->id,
            'kode_prodi' => 'SI01',
            'nama_prodi' => 'Sistem Informasi',
            'jenjang' => 'S1',
            'kaprodi' => null,
            'akreditasi' => 'A',
        ]);

        $mahasiswa = Mahasiswa::create([
            'program_studi_id' => $prodi->id,
            'nim' => 'MHS001',
            'nama_mahasiswa' => 'Budi',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => null,
            'tanggal_lahir' => null,
            'alamat' => null,
            'village_id' => null,
            'telepon' => null,
            'email' => 'budi@example.test',
            'tahun_masuk' => '2025',
            'semester' => 1,
            'ipk' => 0.00,
            'status' => 'Aktif',
            'nama_wali' => null,
            'telepon_wali' => null,
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'mahasiswa_id' => $mahasiswa->id,
        ]);
    }

    public function test_profil_mahasiswa_export_excel_returns_excel_response(): void
    {
        $user = $this->makeMahasiswaUser();
        $this->actingAs($user);

        $response = $this->get(route('profil-mahasiswa.exportExcel'));
        $response->assertStatus(200);
        $this->assertTrue(str_contains((string) $response->headers->get('Content-Type'), 'application/vnd.ms-excel'));
        $response->assertSee('Budi');
    }

    public function test_profil_mahasiswa_export_pdf_returns_pdf_response(): void
    {
        $user = $this->makeMahasiswaUser();
        $this->actingAs($user);

        $response = $this->get(route('profil-mahasiswa.exportPdf'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}

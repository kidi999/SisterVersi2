<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;
use App\Models\ProgramStudi;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        // Fakultas Teknik
        $ft = Fakultas::create([
            'kode_fakultas' => 'FT',
            'nama_fakultas' => 'Fakultas Teknik',
            'singkatan' => 'FT',
            'dekan' => 'Prof. Dr. Ir. Ahmad Suryadi, M.T.',
            'alamat' => 'Jl. Kampus No. 1',
            'telepon' => '021-12345678',
            'email' => 'ft@university.ac.id'
        ]);

        ProgramStudi::create([
            'fakultas_id' => $ft->id,
            'kode_prodi' => 'TI',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. Budi Santoso, M.Kom',
            'akreditasi' => 'A'
        ]);

        ProgramStudi::create([
            'fakultas_id' => $ft->id,
            'kode_prodi' => 'SI',
            'nama_prodi' => 'Sistem Informasi',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. Siti Nurhaliza, M.Kom',
            'akreditasi' => 'A'
        ]);

        // Fakultas Ekonomi
        $fe = Fakultas::create([
            'kode_fakultas' => 'FE',
            'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',
            'singkatan' => 'FEB',
            'dekan' => 'Prof. Dr. Hj. Dewi Kusuma, S.E., M.M.',
            'alamat' => 'Jl. Kampus No. 2',
            'telepon' => '021-87654321',
            'email' => 'feb@university.ac.id'
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fe->id,
            'kode_prodi' => 'MNJ',
            'nama_prodi' => 'Manajemen',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. Eko Prasetyo, S.E., M.M.',
            'akreditasi' => 'A'
        ]);

        ProgramStudi::create([
            'fakultas_id' => $fe->id,
            'kode_prodi' => 'AKT',
            'nama_prodi' => 'Akuntansi',
            'jenjang' => 'S1',
            'kaprodi' => 'Dr. Fitri Amelia, S.E., M.Ak.',
            'akreditasi' => 'B'
        ]);
    }
}

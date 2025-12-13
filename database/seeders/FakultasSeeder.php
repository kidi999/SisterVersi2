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
        $ft = Fakultas::query()->updateOrCreate(
            ['kode_fakultas' => 'FT'],
            [
                'nama_fakultas' => 'Fakultas Teknik',
                'singkatan' => 'FT',
                'dekan' => 'Prof. Dr. Ir. Ahmad Suryadi, M.T.',
                'alamat' => 'Jl. Kampus No. 1',
                'telepon' => '021-12345678',
                'email' => 'ft@university.ac.id'
            ]
        );

        ProgramStudi::query()->updateOrCreate(
            ['kode_prodi' => 'TI'],
            [
                'fakultas_id' => $ft->id,
                'nama_prodi' => 'Teknik Informatika',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Budi Santoso, M.Kom',
                'akreditasi' => 'A'
            ]
        );

        ProgramStudi::query()->updateOrCreate(
            ['kode_prodi' => 'SI'],
            [
                'fakultas_id' => $ft->id,
                'nama_prodi' => 'Sistem Informasi',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Siti Nurhaliza, M.Kom',
                'akreditasi' => 'A'
            ]
        );

        // Fakultas Ekonomi
        $fe = Fakultas::query()->updateOrCreate(
            ['kode_fakultas' => 'FE'],
            [
                'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis',
                'singkatan' => 'FEB',
                'dekan' => 'Prof. Dr. Hj. Dewi Kusuma, S.E., M.M.',
                'alamat' => 'Jl. Kampus No. 2',
                'telepon' => '021-87654321',
                'email' => 'feb@university.ac.id'
            ]
        );

        ProgramStudi::query()->updateOrCreate(
            ['kode_prodi' => 'MNJ'],
            [
                'fakultas_id' => $fe->id,
                'nama_prodi' => 'Manajemen',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Eko Prasetyo, S.E., M.M.',
                'akreditasi' => 'A'
            ]
        );

        ProgramStudi::query()->updateOrCreate(
            ['kode_prodi' => 'AKT'],
            [
                'fakultas_id' => $fe->id,
                'nama_prodi' => 'Akuntansi',
                'jenjang' => 'S1',
                'kaprodi' => 'Dr. Fitri Amelia, S.E., M.Ak.',
                'akreditasi' => 'B'
            ]
        );
    }
}

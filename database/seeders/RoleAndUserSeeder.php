<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles (idempotent)
        $superAdmin = Role::query()->updateOrCreate(
            ['name' => Role::SUPER_ADMIN],
            [
                'display_name' => 'Super Admin',
                'description' => 'Akses penuh ke seluruh sistem',
                'created_by' => 'System',
            ]
        );

        $adminUniv = Role::query()->updateOrCreate(
            ['name' => Role::ADMIN_UNIVERSITAS],
            [
                'display_name' => 'Admin Universitas',
                'description' => 'Administrator tingkat universitas',
                'created_by' => 'System',
            ]
        );

        $adminFakultas = Role::query()->updateOrCreate(
            ['name' => Role::ADMIN_FAKULTAS],
            [
                'display_name' => 'Admin Fakultas',
                'description' => 'Administrator tingkat fakultas',
                'created_by' => 'System',
            ]
        );

        $adminProdi = Role::query()->updateOrCreate(
            ['name' => Role::ADMIN_PRODI],
            [
                'display_name' => 'Admin Program Studi',
                'description' => 'Administrator tingkat program studi',
                'created_by' => 'System',
            ]
        );

        Role::query()->updateOrCreate(
            ['name' => Role::DOSEN],
            [
                'display_name' => 'Dosen',
                'description' => 'Dosen pengajar',
                'created_by' => 'System',
            ]
        );

        Role::query()->updateOrCreate(
            ['name' => Role::MAHASISWA],
            [
                'display_name' => 'Mahasiswa',
                'description' => 'Mahasiswa aktif',
                'created_by' => 'System',
            ]
        );

        $fakultasTeknikId = Fakultas::query()->where('kode_fakultas', 'FT')->value('id');
        $prodiTiId = ProgramStudi::query()->where('kode_prodi', 'TI')->value('id');

        // Create default users (idempotent)
        User::query()->updateOrCreate(
            ['email' => 'superadmin@sister.ac.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role_id' => $superAdmin->id,
                'is_active' => true,
                'created_by' => 'System',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@sister.ac.id'],
            [
                'name' => 'Admin Universitas',
                'password' => Hash::make('password'),
                'role_id' => $adminUniv->id,
                'is_active' => true,
                'created_by' => 'System',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin.ft@sister.ac.id'],
            [
                'name' => 'Admin Fakultas Teknik',
                'password' => Hash::make('password'),
                'role_id' => $adminFakultas->id,
                'fakultas_id' => $fakultasTeknikId,
                'is_active' => true,
                'created_by' => 'System',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin.ti@sister.ac.id'],
            [
                'name' => 'Admin Prodi TI',
                'password' => Hash::make('password'),
                'role_id' => $adminProdi->id,
                'fakultas_id' => $fakultasTeknikId,
                'program_studi_id' => $prodiTiId,
                'is_active' => true,
                'created_by' => 'System',
            ]
        );
    }
}

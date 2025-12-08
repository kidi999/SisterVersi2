<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $superAdmin = Role::create([
            'name' => Role::SUPER_ADMIN,
            'display_name' => 'Super Admin',
            'description' => 'Akses penuh ke seluruh sistem',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $adminUniv = Role::create([
            'name' => Role::ADMIN_UNIVERSITAS,
            'display_name' => 'Admin Universitas',
            'description' => 'Administrator tingkat universitas',
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        $adminFakultas = Role::create([
            'name' => Role::ADMIN_FAKULTAS,
            'display_name' => 'Admin Fakultas',
            'description' => 'Administrator tingkat fakultas',
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        $adminProdi = Role::create([
            'name' => Role::ADMIN_PRODI,
            'display_name' => 'Admin Program Studi',
            'description' => 'Administrator tingkat program studi',
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        $dosen = Role::create([
            'name' => Role::DOSEN,
            'display_name' => 'Dosen',
            'description' => 'Dosen pengajar',
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        $mahasiswa = Role::create([
            'name' => Role::MAHASISWA,
            'display_name' => 'Mahasiswa',
            'description' => 'Mahasiswa aktif',
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        // Create default users
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@sister.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $superAdmin->id,
            'is_active' => true,
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        User::create([
            'name' => 'Admin Universitas',
            'email' => 'admin@sister.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $adminUniv->id,
            'is_active' => true,
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        User::create([
            'name' => 'Admin Fakultas Teknik',
            'email' => 'admin.ft@sister.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $adminFakultas->id,
            'fakultas_id' => 1, // Fakultas Teknik
            'is_active' => true,
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);

        User::create([
            'name' => 'Admin Prodi TI',
            'email' => 'admin.ti@sister.ac.id',
            'password' => Hash::make('password'),
            'role_id' => $adminProdi->id,
            'fakultas_id' => 1,
            'program_studi_id' => 1, // Teknik Informatika
            'is_active' => true,
            'inserted_by' => 'System',
            'inserted_at' => now()
        ]);
    }
}

<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;

// Cari atau buat role super_admin
$role = Role::firstOrCreate(
    ['name' => 'super_admin'],
    [
        'display_name' => 'Super Admin',
        'description' => 'Super administrator',
        'created_by' => 'system',
    ]
);

// Buat user super admin
User::create([
    'name' => 'Super Admin',
    'email' => 'superadmin@sister.local',
    'password' => Hash::make('superadmin123'),
    'role_id' => $role->id,
    'is_active' => true,
    'email_verified_at' => now(),
    'created_by' => 'system',
    'remember_token' => Str::random(10),
]);

echo "User super admin berhasil dibuat:\n";
echo "Email: superadmin@sister.local\n";
echo "Password: superadmin123\n";

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Province;
use App\Models\Regency;
use App\Models\SubRegency;
use App\Models\Village;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        // Provinsi DKI Jakarta
        $jakarta = Province::create([
            'code' => '31',
            'name' => 'DKI Jakarta',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $jaksel = Regency::create([
            'province_id' => $jakarta->id,
            'code' => '3174',
            'name' => 'Jakarta Selatan',
            'type' => 'Kota',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $kebayoran = SubRegency::create([
            'regency_id' => $jaksel->id,
            'code' => '317401',
            'name' => 'Kebayoran Baru',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $kebayoran->id,
            'code' => '31740101',
            'name' => 'Gunung',
            'postal_code' => '12120',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $kebayoran->id,
            'code' => '31740102',
            'name' => 'Kramat Pela',
            'postal_code' => '12130',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        // Provinsi Jawa Barat
        $jabar = Province::create([
            'code' => '32',
            'name' => 'Jawa Barat',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $bandung = Regency::create([
            'province_id' => $jabar->id,
            'code' => '3273',
            'name' => 'Bandung',
            'type' => 'Kota',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $coblong = SubRegency::create([
            'regency_id' => $bandung->id,
            'code' => '327301',
            'name' => 'Coblong',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $coblong->id,
            'code' => '32730101',
            'name' => 'Cipaganti',
            'postal_code' => '40131',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $coblong->id,
            'code' => '32730102',
            'name' => 'Dago',
            'postal_code' => '40135',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        // Provinsi Jawa Tengah
        $jateng = Province::create([
            'code' => '33',
            'name' => 'Jawa Tengah',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $semarang = Regency::create([
            'province_id' => $jateng->id,
            'code' => '3374',
            'name' => 'Semarang',
            'type' => 'Kota',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        $tembalang = SubRegency::create([
            'regency_id' => $semarang->id,
            'code' => '337401',
            'name' => 'Tembalang',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $tembalang->id,
            'code' => '33740101',
            'name' => 'Tembalang',
            'postal_code' => '50275',
            'created_by' => 'System',
            'created_at' => now()
        ]);

        Village::create([
            'sub_regency_id' => $tembalang->id,
            'code' => '33740102',
            'name' => 'Bulusan',
            'postal_code' => '50277',
            'created_by' => 'System',
            'created_at' => now()
        ]);
    }
}

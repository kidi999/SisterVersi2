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
        $jakarta = Province::firstOrCreate(
            ['code' => '31'],
            ['name' => 'DKI Jakarta', 'created_by' => 'System']
        );

        $jaksel = Regency::firstOrCreate(
            ['code' => '3174'],
            [
                'province_id' => $jakarta->id,
                'name' => 'Jakarta Selatan',
                'type' => 'Kota',
                'created_by' => 'System',
            ]
        );

        $kebayoran = SubRegency::firstOrCreate(
            ['code' => '317401'],
            [
                'regency_id' => $jaksel->id,
                'name' => 'Kebayoran Baru',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '31740101'],
            [
                'sub_regency_id' => $kebayoran->id,
                'name' => 'Gunung',
                'postal_code' => '12120',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '31740102'],
            [
                'sub_regency_id' => $kebayoran->id,
                'name' => 'Kramat Pela',
                'postal_code' => '12130',
                'created_by' => 'System',
            ]
        );

        // Provinsi Jawa Barat
        $jabar = Province::firstOrCreate(
            ['code' => '32'],
            ['name' => 'Jawa Barat', 'created_by' => 'System']
        );

        $bandung = Regency::firstOrCreate(
            ['code' => '3273'],
            [
                'province_id' => $jabar->id,
                'name' => 'Bandung',
                'type' => 'Kota',
                'created_by' => 'System',
            ]
        );

        $coblong = SubRegency::firstOrCreate(
            ['code' => '327301'],
            [
                'regency_id' => $bandung->id,
                'name' => 'Coblong',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '32730101'],
            [
                'sub_regency_id' => $coblong->id,
                'name' => 'Cipaganti',
                'postal_code' => '40131',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '32730102'],
            [
                'sub_regency_id' => $coblong->id,
                'name' => 'Dago',
                'postal_code' => '40135',
                'created_by' => 'System',
            ]
        );

        // Provinsi Jawa Tengah
        $jateng = Province::firstOrCreate(
            ['code' => '33'],
            ['name' => 'Jawa Tengah', 'created_by' => 'System']
        );

        $semarang = Regency::firstOrCreate(
            ['code' => '3374'],
            [
                'province_id' => $jateng->id,
                'name' => 'Semarang',
                'type' => 'Kota',
                'created_by' => 'System',
            ]
        );

        $tembalang = SubRegency::firstOrCreate(
            ['code' => '337401'],
            [
                'regency_id' => $semarang->id,
                'name' => 'Tembalang',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '33740101'],
            [
                'sub_regency_id' => $tembalang->id,
                'name' => 'Tembalang',
                'postal_code' => '50275',
                'created_by' => 'System',
            ]
        );

        Village::firstOrCreate(
            ['code' => '33740102'],
            [
                'sub_regency_id' => $tembalang->id,
                'name' => 'Bulusan',
                'postal_code' => '50277',
                'created_by' => 'System',
            ]
        );
    }
}

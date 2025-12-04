<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HariLibur;
use Carbon\Carbon;

class HariLiburSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = date('Y');
        
        $hariLibur = [
            // Libur Nasional
            [
                'nama' => 'Tahun Baru Masehi',
                'tanggal' => Carbon::create($year, 1, 1),
                'jenis' => 'Nasional',
                'keterangan' => 'Tahun Baru ' . $year,
                'is_recurring' => true,
            ],
            [
                'nama' => 'Hari Raya Idul Fitri',
                'tanggal' => Carbon::create($year, 4, 10),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Hari Raya Idul Fitri ' . $year,
                'is_recurring' => false,
            ],
            [
                'nama' => 'Cuti Bersama Idul Fitri',
                'tanggal' => Carbon::create($year, 4, 11),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Cuti Bersama Idul Fitri ' . $year,
                'is_recurring' => false,
            ],
            [
                'nama' => 'Hari Buruh Internasional',
                'tanggal' => Carbon::create($year, 5, 1),
                'jenis' => 'Nasional',
                'keterangan' => 'Hari Buruh/May Day',
                'is_recurring' => true,
            ],
            [
                'nama' => 'Kenaikan Isa Al Masih',
                'tanggal' => Carbon::create($year, 5, 9),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Hari Kenaikan Yesus Kristus',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Hari Raya Waisak',
                'tanggal' => Carbon::create($year, 5, 23),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Hari Raya Waisak ' . $year,
                'is_recurring' => false,
            ],
            [
                'nama' => 'Hari Lahir Pancasila',
                'tanggal' => Carbon::create($year, 6, 1),
                'jenis' => 'Nasional',
                'keterangan' => 'Hari Lahir Pancasila',
                'is_recurring' => true,
            ],
            [
                'nama' => 'Hari Raya Idul Adha',
                'tanggal' => Carbon::create($year, 6, 17),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Hari Raya Idul Adha ' . $year,
                'is_recurring' => false,
            ],
            [
                'nama' => 'Tahun Baru Islam 1446 H',
                'tanggal' => Carbon::create($year, 7, 7),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Tahun Baru Islam 1446 Hijriah',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Hari Kemerdekaan RI',
                'tanggal' => Carbon::create($year, 8, 17),
                'jenis' => 'Nasional',
                'keterangan' => 'HUT Kemerdekaan Republik Indonesia',
                'is_recurring' => true,
            ],
            [
                'nama' => 'Maulid Nabi Muhammad SAW',
                'tanggal' => Carbon::create($year, 9, 16),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Maulid Nabi Muhammad SAW',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Hari Natal',
                'tanggal' => Carbon::create($year, 12, 25),
                'jenis' => 'Keagamaan',
                'keterangan' => 'Hari Natal ' . $year,
                'is_recurring' => true,
            ],
            
            // Libur Akademik Semester Genap 2024/2025
            [
                'nama' => 'Ujian Tengah Semester Genap',
                'tanggal' => Carbon::create($year, 3, 25),
                'jenis' => 'Akademik',
                'keterangan' => 'UTS Semester Genap 2024/2025',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Ujian Akhir Semester Genap',
                'tanggal' => Carbon::create($year, 6, 10),
                'jenis' => 'Akademik',
                'keterangan' => 'UAS Semester Genap 2024/2025',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Libur Semester Genap',
                'tanggal' => Carbon::create($year, 7, 1),
                'jenis' => 'Akademik',
                'keterangan' => 'Libur Semester Genap 2024/2025',
                'is_recurring' => false,
            ],
            
            // Libur Akademik Semester Ganjil 2025/2026
            [
                'nama' => 'Ujian Tengah Semester Ganjil',
                'tanggal' => Carbon::create($year, 10, 21),
                'jenis' => 'Akademik',
                'keterangan' => 'UTS Semester Ganjil 2025/2026',
                'is_recurring' => false,
            ],
            [
                'nama' => 'Ujian Akhir Semester Ganjil',
                'tanggal' => Carbon::create($year, 12, 16),
                'jenis' => 'Akademik',
                'keterangan' => 'UAS Semester Ganjil 2025/2026',
                'is_recurring' => false,
            ],
        ];

        foreach ($hariLibur as $libur) {
            HariLibur::create($libur);
        }
    }
}

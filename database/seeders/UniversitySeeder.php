<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\University;
use App\Models\Village;
use App\Models\SubRegency;
use App\Models\Regency;
use App\Models\Province;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan ada provinsi, kabupaten, kecamatan, desa
        $province = Province::first() ?? Province::factory()->create(['name' => 'Jawa Tengah']);
        $regency = Regency::first() ?? Regency::factory()->create(['province_id' => $province->id, 'name' => 'Kabupaten Contoh', 'type' => 'Kabupaten']);
        $subRegency = SubRegency::first() ?? SubRegency::factory()->create(['regency_id' => $regency->id, 'name' => 'Kecamatan Contoh']);
        $village = Village::first() ?? Village::factory()->create(['sub_regency_id' => $subRegency->id, 'name' => 'Desa Contoh']);

        University::updateOrCreate(
            ['kode' => 'UNI001'],
            [
                'nama' => 'Universitas SISTER',
                'singkatan' => 'USIS',
                'jenis' => 'Negeri',
                'status' => 'Aktif',
                'akreditasi' => 'A',
                'no_sk_akreditasi' => 'SK-2025/AKR',
                'tanggal_akreditasi' => now(),
                'tanggal_berakhir_akreditasi' => now()->addYears(5),
                'no_sk_pendirian' => 'SK-2020/PND',
                'tanggal_pendirian' => now()->subYears(5),
                'no_izin_operasional' => 'SK-2020/OPR',
                'tanggal_izin_operasional' => now()->subYears(5),
                'rektor' => 'Dr. Rektor SISTER',
                'nip_rektor' => '198001011999031001',
                'wakil_rektor_1' => 'Dr. Wakil 1',
                'wakil_rektor_2' => 'Dr. Wakil 2',
                'wakil_rektor_3' => 'Dr. Wakil 3',
                'wakil_rektor_4' => 'Dr. Wakil 4',
                'email' => 'info@sister.ac.id',
                'telepon' => '024-1234567',
                'fax' => '024-1234568',
                'website' => 'https://sister.ac.id',
                'alamat' => 'Jl. SISTER No. 1',
                'village_id' => $village->id,
                'kode_pos' => '50123',
                'visi' => 'Menjadi universitas digital terbaik.',
                'misi' => 'Mencetak lulusan unggul dan berintegritas.',
                'sejarah' => 'Didirikan tahun 2020.',
                'keterangan' => 'Universitas contoh untuk SISTER.',
                'created_by' => 'Seeder',
            ]
        );
    }
}

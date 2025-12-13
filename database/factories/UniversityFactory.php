<?php

namespace Database\Factories;

use App\Models\University;
use App\Models\Village;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniversityFactory extends Factory
{
    protected $model = University::class;

    public function definition()
    {
        return [
            'kode' => $this->faker->unique()->bothify('UNI###'),
            'nama' => $this->faker->company . ' University',
            'singkatan' => strtoupper($this->faker->lexify('UNI??')),
            'jenis' => $this->faker->randomElement(['Negeri', 'Swasta']),
            'status' => 'Aktif',
            'akreditasi' => $this->faker->randomElement(['A', 'B', 'C']),
            'no_sk_akreditasi' => $this->faker->bothify('SK-####/AKR'),
            'tanggal_akreditasi' => $this->faker->date(),
            'tanggal_berakhir_akreditasi' => $this->faker->date(),
            'no_sk_pendirian' => $this->faker->bothify('SK-####/PND'),
            'tanggal_pendirian' => $this->faker->date(),
            'no_izin_operasional' => $this->faker->bothify('SK-####/OPR'),
            'tanggal_izin_operasional' => $this->faker->date(),
            'rektor' => $this->faker->name,
            'nip_rektor' => $this->faker->numerify('19##########'),
            'wakil_rektor_1' => $this->faker->name,
            'wakil_rektor_2' => $this->faker->name,
            'wakil_rektor_3' => $this->faker->name,
            'wakil_rektor_4' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'telepon' => $this->faker->phoneNumber,
            'fax' => $this->faker->phoneNumber,
            'website' => $this->faker->url,
            'alamat' => $this->faker->address,
            'village_id' => Village::factory(),
            'kode_pos' => $this->faker->postcode,
            'logo_path' => null,
            'nama_file_logo' => null,
            'visi' => $this->faker->sentence(10),
            'misi' => $this->faker->paragraph(2),
            'sejarah' => $this->faker->paragraph(3),
            'keterangan' => $this->faker->sentence(8),
            'created_by' => 'Seeder',
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}

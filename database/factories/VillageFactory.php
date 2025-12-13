<?php

namespace Database\Factories;

use App\Models\Village;
use App\Models\SubRegency;
use Illuminate\Database\Eloquent\Factories\Factory;

class VillageFactory extends Factory
{
    protected $model = Village::class;

    public function definition()
    {
        return [
            'sub_regency_id' => SubRegency::factory(),
            'code' => $this->faker->unique()->numerify('VLG###'),
            'name' => $this->faker->citySuffix . ' ' . $this->faker->city,
            'postal_code' => $this->faker->postcode,
            'created_by' => 'Seeder',
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}

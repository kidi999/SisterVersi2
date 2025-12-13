<?php

namespace Database\Factories;

use App\Models\Regency;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class RegencyFactory extends Factory
{
    protected $model = Regency::class;

    public function definition(): array
    {
        return [
            'province_id' => Province::factory(),
            'code' => $this->faker->unique()->numerify('K###'),
            'name' => $this->faker->unique()->city(),
            'type' => $this->faker->randomElement(['Kabupaten', 'Kota']),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProvinceFactory extends Factory
{
    protected $model = Province::class;

    public function definition()
    {
        return [
            'code' => $this->faker->unique()->numerify('ID##'),
            'name' => $this->faker->unique()->state(),
            'created_by' => 'Seeder',
        ];
    }
}

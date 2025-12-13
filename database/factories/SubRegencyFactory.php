<?php

namespace Database\Factories;

use App\Models\SubRegency;
use App\Models\Regency;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubRegencyFactory extends Factory
{
    protected $model = SubRegency::class;

    public function definition(): array
    {
        return [
            'regency_id' => Regency::factory(),
            'code' => $this->faker->unique()->numerify('S###'),
            'name' => $this->faker->unique()->streetName(),
        ];
    }
}

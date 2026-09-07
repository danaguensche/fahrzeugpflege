<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CarGroup;

class CarGroupFactory extends Factory
{

    public function definition(): array
    {
        return [
            //
            'title' => $this->faker->word(),
        ];
    }
}

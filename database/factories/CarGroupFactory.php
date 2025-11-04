<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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

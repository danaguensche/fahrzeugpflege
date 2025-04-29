<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarGroupFactory extends Factory
{
    public function definition(): array
    {
        $CAR_GROUP_MIN = 1;
        $CAR_GROUP_MAX = 4;
        return [
            'id' => $this->faker->unique()->numberBetween($CAR_GROUP_MIN, $CAR_GROUP_MAX),
            'title' => $this->faker->word()
        ];
    }
}

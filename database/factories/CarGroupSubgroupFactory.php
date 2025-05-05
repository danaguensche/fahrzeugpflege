<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CarGroup;

class CarGroupSubgroupFactory extends Factory
{

    public function definition(): array
    {
        return [
            //
            'title' => $this->faker->word(),
            'id_car_group' => $this->faker->randomElement(CarGroup::pluck('id'))
        ];
    }
}

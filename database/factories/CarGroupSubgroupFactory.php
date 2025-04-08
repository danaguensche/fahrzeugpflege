<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarGroupSubgroupFactory extends Factory
{

    public function definition(): array
    {
        $subgroupPossibilities = array(
            "Kleinwagen" => 1, "Cabrio" => 1, "Limousine Klein" => 1,
            "Mittelklassewagen Groß" => 2, "Oberklassefahrzeug" => 2, "SUV Klein" => 2, "Minivan Klein" => 2, "Kastenwagen Klein" => 2,
            "Transporter" => 3, "SUV Groß" => 3, "Sonderfahrzeug" => 3, "Luxusfahrzeug" => 3
        );
        $newSubgroup = $this->faker->unique()->randomKey($subgroupPossibilities);
        return [
            //
            'title' => $newSubgroup,
            'id_car_group' => $subgroupPossibilities[$newSubgroup]
        ];
    }
}

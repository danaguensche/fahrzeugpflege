<?php

namespace Database\Factories;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class CarFactory extends Factory
{
    public function definition(): array
    {
        $maxSubgroupId = DB::table('car_group_subgroups')->get()->max('id');

        return [
            'customer_id' => Customer::factory(),
            'kennzeichen' => $this->faker->regexify('[A-Z]{1,3} [A-Z]{1,2} [1-9][0-9]{1,3}'),
            'Fahrzeugklasse' => $this->faker->numberBetween(1, $maxSubgroupId),
            'automarke' => $this->faker->randomElement(['Volkswagen', 'BMW', 'Mercedes-Benz', 'Audi', 'Opel']),
            'typ' => $this->faker->word,
            'farbe' => $this->faker->colorName,
            'sonstiges' => $this->faker->optional()->sentence,
            'image' => $this->faker->imageUrl(640, 480, 'cars', true),
        ];
    }
}

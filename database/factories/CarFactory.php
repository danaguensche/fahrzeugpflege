<?php

namespace Database\Factories;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'kennzeichen' => $this->faker->regexify('[A-Z]{1,3} [A-Z]{1,2} [1-9][0-9]{1,3}'),
            'Fahrzeugklasse' => 1,
            'automarke' => $this->faker->randomElement(['Volkswagen', 'BMW', 'Mercedes-Benz', 'Audi', 'Opel']),
            'typ' => $this->faker->word,
            'farbe' => $this->faker->colorName,
            'sonstiges' => $this->faker->optional()->sentence,
        ];
    }
}

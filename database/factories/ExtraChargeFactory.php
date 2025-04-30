<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PriceCondition;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExtraCharge>
 */
class ExtraChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'price' => $this->faker->randomFloat(2, 1, 100),
            'id_price_condition' => $this->faker->randomElement(PriceCondition::pluck('id')),
            'comment' => $this->faker->optional()->word()
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\ExtraCharge;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderExtraCharge>
 */
class OrderExtraChargeFactory extends Factory
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
            'id_order' => $this->faker->randomElement(Order::pluck('id')),
            'id_extra_charge' => $this->faker->randomElement(ExtraCharge::pluck('id'))
        ];
    }
}

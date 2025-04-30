<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Service;
use App\Models\PriceCondition;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServicePricing>
 */
class ServicePricingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $CAR_GROUP_MIN = 1;
        $CAR_GROUP_MAX = 4;
        
        return [
            //
            'id_service' => $this->faker->randomElement(Service::pluck('id')),
            'id_car_group' => $this->faker->numberBetween($CAR_GROUP_MIN, $CAR_GROUP_MAX),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'id_price_condition' => $this->faker->randomElement(PriceCondition::pluck('id'))
        ];
    }
}

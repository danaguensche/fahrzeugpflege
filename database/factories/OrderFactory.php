<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;
use App\Models\ServicePricing;
use App\Models\Car;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'id_customer' => $this->faker->randomElement(Customer::pluck('id')),
            'id_car' => $this->faker->randomElement(Car::pluck('id')),
            'id_service_pricing' => $this->faker->randomElement(ServicePricing::pluck('id')),
            'created_at' => $this->faker->date('Y-m-d') . ' ' . $this->faker->time()
        ];
    }
}

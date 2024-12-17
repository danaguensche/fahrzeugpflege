<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'company' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'phonenumber' => $this->faker->phoneNumber(),
            'addressline' => $this->faker->streetAddress(),
            'postalcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
        ];
    }
}

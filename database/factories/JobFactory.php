<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cleaningStart = fake()->dateTimeBetween('-3 months', 'now');
        
        // Berechne das maximale Ende (5 Stunden später)
        $maxCleaningEnd = (clone $cleaningStart)->modify('+5 hours');
        $cleaningEnd = fake()->dateTimeBetween($cleaningStart, $maxCleaningEnd);
        
        // scheduledAt max. 3 Tage nach cleaning_end
        $maxScheduledAt = (clone $cleaningEnd)->modify('+3 days');
        $scheduledAt = fake()->dateTimeBetween($cleaningEnd, $maxScheduledAt);
        
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->words(10, true),
            'car_id' => \App\Models\Car::factory(),
            'customer_id' => \App\Models\Customer::factory(),
            'status' => fake()->randomElement(['ausstehend', 'in_bearbeitung', 'abgeschlossen', 'im_rueckblick']),
            'cleaning_start' => $cleaningStart,
            'cleaning_end' => $cleaningEnd,
            'scheduled_at' => $scheduledAt,
        ];
    }
}
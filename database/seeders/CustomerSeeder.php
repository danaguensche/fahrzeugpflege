<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()
            ->count((25))
            ->hasCars(3)
            ->create();

        Customer::factory()
            ->count((25))
            ->hasCars(2)
            ->create();

        Customer::factory()
            ->count((25))
            ->hasCars(1)
            ->create();
    }
}

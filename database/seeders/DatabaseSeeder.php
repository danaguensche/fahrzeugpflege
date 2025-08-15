<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CarGroupSeeder::class,
            CarGroupSubgroupSeeder::class,
            ServiceSeeder::class,
            PriceConditionSeeder::class,
            ServicePricingSeeder::class,
            ExtraChargeSeeder::class,
            // CarSeeder::class,
            // CustomerSeeder::class,
            OrderSeeder::class,
            OrderExtraChargeSeeder::class
        ]);
    }
}

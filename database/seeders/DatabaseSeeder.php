<?php

namespace Database\Seeders;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Job;
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
            ServiceSeeder::class,
            CarSeeder::class,
            CustomerSeeder::class,
            JobSeeder::class,
            JobServiceSeeder::class,
            AllowedUsernameSeeder::class,
        ]);
    }
}

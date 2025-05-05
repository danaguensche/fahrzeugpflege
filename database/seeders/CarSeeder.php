<?php
namespace Database\Seeders;

use App\Models\CarGroup;
use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        Car::factory()
            ->count(10)
            ->create();

    }
}
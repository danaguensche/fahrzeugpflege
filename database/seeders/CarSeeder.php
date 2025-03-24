<?php
namespace Database\Seeders;

use App\Models\CarGroup;
use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        CarGroup::factory(5)->create()->each(function ($carGroup) {
            Car::factory(10)->create([
                'Fahrzeugklasse' => $carGroup->id 
            ]);
        });
    }
}
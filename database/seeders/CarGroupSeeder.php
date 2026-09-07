<?php

namespace Database\Seeders;

use App\Models\CarGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CarGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $COUNT_WHEN_NO_FILE = 10;

        $seeding_values = Storage::disk('local')->get('/db_imported_data/data_car_group.txt');

        if ($seeding_values == null) {
            CarGroup::factory()->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $titles = array_filter(array_map('trim', explode("\n", $seeding_values)));
            foreach ($titles as $title) {
                CarGroup::create(['title' => $title]);
            }
        }
    }
}
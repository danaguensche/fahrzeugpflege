<?php

namespace Database\Seeders;

use App\Models\CarGroupSubgroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CarGroupSubgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $COUNT_WHEN_NO_FILE = 10;

        $seeding_values = Storage::disk('local')->get('/db_import_data/data_car_group_subgroups.txt');

        $cgsg_factory = CarGroupSubgroup::factory();
        if ($seeding_values == null) {
            $cgsg_factory->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $seeding_values = explode("\n", $seeding_values);
            foreach ($seeding_values as $entry_data) {
                $entry_data = explode("|", trim($entry_data)); // car group, title
                $cgsg_factory->count(1)->create([
                    'title' => $entry_data[1],
                    'id_car_group' => $entry_data[0]
                ]);
            }
        }
    
    }
}

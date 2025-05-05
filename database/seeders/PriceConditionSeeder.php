<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PriceCondition;
use Illuminate\Support\Facades\Storage;

class PriceConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $COUNT_WHEN_NO_FILE = 5;

        $seeding_values = Storage::disk('local')->get('/db_import_data/data_price_conditions.txt');

        $price_condition_factory = PriceCondition::factory();
        if ($seeding_values == null) {
            $price_condition_factory->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $seeding_values = explode("\n", $seeding_values);
            foreach ($seeding_values as $condition_name) {
                $price_condition_factory->count(1)->create(['condition' => trim($condition_name)]);
            }
        }
    }
}

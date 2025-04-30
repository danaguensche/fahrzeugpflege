<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\ExtraCharge;

class ExtraChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $COUNT_WHEN_NO_FILE = 10;

        $seeding_values = Storage::disk('local')->get('/db_import_data/data_extra_charges.txt');

        $e_c_factory = ExtraCharge::factory();
        if ($seeding_values == null) {
            $e_c_factory->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $conditions = array();
            $price_condition_conditions = DB::table('price_conditions')->pluck('condition');
            foreach ($price_condition_conditions as $condition) {
                $conditions[$condition] = DB::table('price_conditions')->where('condition', $condition)->value('id');
            }
            $seeding_values = explode("\n", $seeding_values);
            foreach ($seeding_values as $entry_data) {
                $entry_data = explode("|", trim($entry_data)); // price, price_condition, optional comment
                $e_c_factory->count(1)->create([
                    'price' => $entry_data[0],
                    'id_price_condition' => $conditions[$entry_data[1]],
                    'comment' => count($entry_data) > 2 ? $entry_data[2] : null
                ]);
            }
        }
    }
}

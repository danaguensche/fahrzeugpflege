<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ServicePricing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ServicePricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $COUNT_WHEN_NO_FILE = 15;

        $seeding_values = Storage::disk('local')->get('/db_import_data/data_service_pricings.txt');

        $service_pricing_factory = ServicePricing::factory();
        if ($seeding_values == null) {
            $service_pricing_factory->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $service_titles = DB::table('services')->pluck('title');
            $price_condition_conditions = DB::table('price_conditions')->pluck('condition');
            $services = array();
            $conditions = array();
            foreach ($service_titles as $title) {
                $services[$title] = DB::table('services')->where('title', $title)->value('id');
            }
            foreach ($price_condition_conditions as $condition) {
                $conditions[$condition] = DB::table('price_conditions')->where('condition', $condition)->value('id');
            }

            $seeding_values = explode("\n", $seeding_values);
            foreach ($seeding_values as $entry_data) {
                $entry_data = explode("|", trim($entry_data)); // service title, id_car_group, price, price_condition condition
                $service_pricing_factory->count(1)->create([
                    'id_service' => $services[$entry_data[0]],
                    'id_car_group' => $entry_data[1],
                    'price' => $entry_data[2],
                    'id_price_condition' => $conditions[$entry_data[3]]
                ]);
            }
        }
    }
}

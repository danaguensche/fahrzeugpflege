<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $COUNT_WHEN_NO_FILE = 5;

        $seeding_values = Storage::disk('local')->get('/db_import_data/data_services.txt');

        $service_factory = Service::factory();
        if ($seeding_values == null) {
            $service_factory->count($COUNT_WHEN_NO_FILE)->create();
        } else {
            $seeding_values = explode("\n", $seeding_values);
            foreach ($seeding_values as $service_name) {
                $service_factory->count(1)->create(['title' => trim($service_name)]);
            }
        }
        
    }
}

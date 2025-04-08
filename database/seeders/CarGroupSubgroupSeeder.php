<?php

namespace Database\Seeders;

use App\Models\CarGroupSubgroup;
use Illuminate\Database\Seeder;

class CarGroupSubgroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $POSSIBLE_SUBGROUPS = 12;
        CarGroupSubgroup::factory()
            ->count($POSSIBLE_SUBGROUPS)
            ->create();
    
    }
}

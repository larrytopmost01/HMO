<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CostCentreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comprehensive_check_cost_centres')->insert([
            [
                'name' => 'mb',
                'price' => 35000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mp',
                'price' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'wb',
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'wp',
                'price' => 55000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bc',
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pe',
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'csm',
                'price' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'csw',
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

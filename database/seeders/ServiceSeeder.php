<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('services')->insert([
            [
            'name' => strtolower('dental'),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => strtolower('optical'),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => strtolower('comprehensive'),
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => strtolower('cancer'),
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
    }
}

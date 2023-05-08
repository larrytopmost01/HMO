<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

function hospitalLevelFormatter($name, $level, $point)
{
    /**
     * type: single|family|mixed(single & family),
     * sex: female|mixed(male & female)
     */

    return [
        'name' => $name,
        'level' => $level,
        'point' => $point,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

class HospitalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hospital_levels')->insert([
            hospitalLevelFormatter('guard', 1, 3),
            hospitalLevelFormatter('guard 1', 2, 4),
            hospitalLevelFormatter('guard 2', 3, 5),
            hospitalLevelFormatter('shield', 4, 6),
            hospitalLevelFormatter('shield 1', 5, 7),
            hospitalLevelFormatter('premium', 6, 8),
            hospitalLevelFormatter('premium 1', 7, 9),
            hospitalLevelFormatter('premium 2', 8, 10),
            hospitalLevelFormatter('exclusive', 9, 20),
            hospitalLevelFormatter('special', 10, 25)
        ]);
    }
}
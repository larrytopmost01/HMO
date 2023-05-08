<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\DrugRefill;
use Illuminate\Support\Facades\DB;

class DrugRefillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('drug_refills')->insert([
            [
                'user_id' => 2,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'pending',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'approved',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'declined',
                'reason'=> $faker->text,
                'drug_name' => $faker->word,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
    }
}

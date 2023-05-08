<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('subscriptions')->insert([
            [
                'user_id' => 2,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'guard',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'guard',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'guard',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'custom',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'shield',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'shield',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'shield',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'custom',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'premium',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'premium',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'premium',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'premium',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'exclusives',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'active',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+1 years'),
                'plan_name'=> 'exclusives',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'exclusives',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'inactive',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'exclusives',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 20,
                'status' => 'pending',
                'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'end_date' => date('Y/m/d'),
                'plan_name'=> 'custom',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

        ]);

    }
}

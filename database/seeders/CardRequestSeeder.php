<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\EnrolleeCardRequest;
use Illuminate\Support\Facades\DB;

class CardRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('enrollee_request_cards')->insert([
            [
                'user_id' => 2,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(2)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(3)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(4)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(5)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(6)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'transaction_id' =>$faker->numberBetween(1, 100),
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(7)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(8)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(9)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'approved',
                'transaction_id' => null,
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'declined',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(10)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'pending',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(11)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'pending',
                'transaction_id' => null,
                'enrollee_id' => User::find(11)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(11)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(11)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(12)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(12)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(13)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(13)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(14)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(14)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(15)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(15)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(16)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(16)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'approved',
                'transaction_id' => $faker->numberBetween(1, 100),
                'enrollee_id' => User::find(17)->enrollee->enrollee_id,
                'card_collected'=> false,
                'passport_url' => $faker->imageUrl(640, 480, 'people'),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'declined',
                'transaction_id' => null,
                'enrollee_id' => User::find(17)->enrollee->enrollee_id,
                'card_collected'=> true,
                'passport_url' => null,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
    }
}

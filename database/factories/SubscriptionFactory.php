<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Enrollee;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
 */

$factory->define(\App\Models\Subscription::class, function (Faker $faker) {
 return [
    'user_id' => 1,
    'plan_name' => $faker->randomElement(['guard', 'shield', 'premium', 'exclusives', 'custom', 'special']),
    'status' =>  $faker->randomElement(['active', 'inactive', 'pending']),
    'start_date' => $faker->dateTimeBetween('-1 years', 'now'),
    'end_date' => $faker->dateTimeBetween('now', '+1 years'),
    ];
});
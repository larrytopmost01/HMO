<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\EnrolleeRequestCard;
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

$factory->define(EnrolleeRequestCard::class, function (Faker $faker) {
  return [
    'user_id'=> function () {
        return User::all()->random();
    },
    'enrollee_id'      => Str::random(10),
    'status'          => $faker->randomElement(['pending', 'approved', 'declined']),
    'card_collected'     => $collected = $faker->randomElement([false, true]),
    'passport_url'        => $collected == false ? $faker->imageUrl(640, 480) : null,
    'transaction_id' => $collected == true ? $faker->randomNumber(6) : null
  ];
});
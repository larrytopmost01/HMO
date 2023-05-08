<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
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

$factory->define(User::class, function (Faker $faker) {
  return [
    'role_id' => 1,
    'last_name'      => $faker->firstName(),
    'first_name'     => $faker->lastName(),
    'email'          => $faker->unique()->safeEmail,
    'phone_number'   => $faker->numerify('###-####-###'),
    'is_verified'   => $faker->randomElement([false, true]),
    'is_blocked'   => false,
    'password'       => Hash::make('password'),
  ];
});
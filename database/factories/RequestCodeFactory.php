<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\EnrolleeRequestCode;
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

$factory->define(EnrolleeRequestCode::class, function (Faker $faker) {
  return [
    'user_id'=> function () {
        return User::all()->random();
    },
    'enrollee_id'      => Str::random(10),
    'status'          => $status = $faker->randomElement(['pending', 'approved', 'declined']),
    'hospital_name'     => $faker->city(),
    'request_message'        => $faker->sentence,
    'request_code' => $status == 'approved' ? $faker->numerify('###-###-##') : null,
  ];
});
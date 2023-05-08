<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\HealthCareAppointment;
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

$factory->define(HealthCareAppointment::class, function (Faker $faker) {
  return [
    'user_id'=> function () {
        return User::all()->random();
    },
    'service_name' => $faker->randomElement(['dental', 'optical', 'comprehensive']),
    'comment' => $faker->sentence,
    'status'          => $faker->randomElement(['pending', 'approved']),
    'doctor_name'     => $faker->name,
    'hospital_name'  => $faker->company,
    'comment'        => $faker->sentence,
    'appointment_date' => $faker->dateTimeBetween('-1 years', '+1 years'),
  ];
});
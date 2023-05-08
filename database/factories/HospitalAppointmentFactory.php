<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\HospitalAppointment;
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

$factory->define(HospitalAppointment::class, function (Faker $faker) {
  return [
    'user_id'=> function () {
        return User::all()->random();
    },
    'status'          => $faker->randomElement(['pending', 'approved', 'declined']),
    'hospital_name'     => $faker->city(),
    'doctor_name'        => $faker->name(),
    'comment' => $faker->sentence,
    'appointment_date' => $faker->dateTimeBetween('-1 years', '+1 years'),
  ];
});
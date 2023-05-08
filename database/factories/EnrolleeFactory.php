<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Enrollee;
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

$factory->define(\App\Models\Enrollee::class, function (Faker $faker) {
    
 return [
    'user_id'=> function () {
        return User::all()->random();
    },
    'enrollee_id'=>Str::random(10),
    'company'=>$faker->company(),
    'email'=>$faker->unique()->safeEmail(),
    'phone_number'=>$faker->phoneNumber(),
    'hospital_name'=>$faker->city,
    'is_verified'=> $faker->randomElement([false, true]),
    'plan'=> $faker->randomElement(['guard', 'shield', 'premium', 'exclusives', 'custom', 'special']),
    'name'=>$faker->text(),
    ];
});

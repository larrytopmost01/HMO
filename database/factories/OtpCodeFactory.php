<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\OptCOde;
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

$factory->define(OtpCode::class, function (Faker $faker) {
 return [
    'user_id'  => function () {
        return App\Models\User::all()->random();
       },
    'otp_code' => substr(str_shuffle("0123456789013256794501239786"), 0, 6)
 ];
});
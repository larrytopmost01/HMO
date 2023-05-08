<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use League\CommonMark\Util\ArrayCollection;

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

$factory->define(HealthCareService::class, function (Faker $faker) {
$services = json_encode([
    "Physical Examination and Basic Checks"=>["Consultation & Physical Examination (Height, Weight & BMI)","Eye Examination (Visual Acuity, Tonometry, Color Vision and Fundoscopy","CBC","Urinalysis"],
    "Diabetic Screening"=>["FBS"],
    "Hepatitis Screening"=>["HbsAg"],
    "Lipid Screening"=>["Triglyceride","Cholesterol"],
    "Liver Screening"=>["GGT","ALT","AST"],
    "Kidney Screening"=>["Urea","Creatinine"],
    "Heart Checks"=>["Chest Xray","ECG"],
    "Organs Imaging"=>["USS Abdomen"],
    "Cancer Screening"=>["PSA"]]);

  return [
    'services' => $services,
    'service_name' => $faker->randomElement(['dental', 'optical', 'comprehensive']),
    'appointment_id' => function () {
      return HealthCareAppointment::all()->random();
    },
    'transaction_id' => $faker->unique()->numberBetween(1, 100),
    'user_id'=> function () {
        return User::all()->random();
    },
    'amount_paid' => $faker->randomElement(['1000', '25000', '30000']),
  ];
});
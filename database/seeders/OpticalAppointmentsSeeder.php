<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class OpticalAppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $faker = Faker::create();
        DB::table('health_care_appointments')->insert([
            [
                'user_id' => 2,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),               
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 3,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),              
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 4,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 5,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),               
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 5,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 6,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),               
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 6,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 7,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),               
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 8,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),         
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 9,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 10,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'user_id' => 11,
                'service_name' => 'optical',
                'comment' => $faker->text,
                'doctor_name'=>$faker->name(),
                'hospital_name'=> $faker->company,
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),                
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            
        ]);
    }
}
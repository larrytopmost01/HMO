<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\HospitalAppointment;
use Illuminate\Support\Facades\DB;

class HospitalAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('hospital_appointments')->insert([
            [
                'user_id' => 2,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 2,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'pending',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'approved',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'status' => 'declined',
                'hospital_name'=> $faker->city(),
                'doctor_name' => $faker->name(),
                'appointment_date' => $faker->dateTimeBetween('-1 years', 'now'),
                'comment' => $faker->text(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\User;
use App\Utils\UniquePhoneNumberGenerator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        DB::table('users')->insert([
           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456871',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456872',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456873',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456874',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456875',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456876',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456877',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456878',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456879',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456880',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456881',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456882',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456883',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456884',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456885',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456886',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456887',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456888',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456889',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456890',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456891',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456892',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456893',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456894',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456895',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456896',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456897',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456898',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456899',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456900',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456901',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456902',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
        
           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456903',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456904',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456905',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456906',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456907',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456908',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456909',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456910',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456911',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456912',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456913',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456914',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456915',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456916',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456917',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456918',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456919',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456920',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456921',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456922',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456923',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456924',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456925',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456926',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456927',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456928',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456929',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456930',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456931',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456932',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456933',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456934',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456935',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456936',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456937',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456938',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456939',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456940',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456941',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456942',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456943',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456944',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456945',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456946',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456947',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456948',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456949',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],

           [
            'role_id' => 1,
            'last_name'      => $faker->firstName(),
            'first_name'     => $faker->lastName(),
            'email'          => $faker->unique()->safeEmail,
            'phone_number' => '+23408123456950',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
        ]);
    }
}

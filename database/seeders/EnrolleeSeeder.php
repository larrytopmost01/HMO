<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Enrollee;
use Illuminate\Support\Facades\DB;

class EnrolleeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $faker = Faker::create();
        DB::table('enrollees')->insert([
            [
                'user_id' => 2,
                'enrollee_id'=> 'HTC/21/1/A/A',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('###-##-###-##'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'guard',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 3,
                'enrollee_id'=>'HTC/21/1/A/B',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('###-##-####-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'guard',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 4,
                'enrollee_id'=>'HTC/21/1/A/C',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('##-#-####-##'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'guard',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 5,
                'enrollee_id'=>'HTC/21/1/A/D',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('###-##-##-##'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'custom',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 6,
                'enrollee_id'=>'HTC/21/1/A/E',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('###-###-##-##'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'shield',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 7,
                'enrollee_id'=>'HTC/21/1/A/F',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'shield',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 8,
                'enrollee_id'=>'HTC/21/1/A/G',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'shield',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 9,
                'enrollee_id'=>'HTC/21/1/A/H',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'custom',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 10,
                'enrollee_id'=>'HTC/21/1/A/I',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'premium',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 11,
                'enrollee_id'=>'HTC/21/1/A/J',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'premium',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 12,
                'enrollee_id'=>'HTC/21/1/A/K',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'premium',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 13,
                'enrollee_id'=>'HTC/21/1/A/L',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('###-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'premium',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 14,
                'enrollee_id'=>'HTC/21/1/A/M',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'exclusives',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 15,
                'enrollee_id'=>'HTC/21/1/A/N',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'exclusives',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 16,
                'enrollee_id'=>'HTC/21/1/A/O',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'exclusives',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 17,
                'enrollee_id'=>'HTC/21/1/A/P',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'exclusives',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 18,
                'enrollee_id'=>'HTC/21/1/A/V',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> false,
                'plan'=> 'guard',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 19,
                'enrollee_id'=>'HTC/21/1/A/W',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> false,
                'plan'=> 'shield',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],

            [
                'user_id' => 20,
                'enrollee_id'=> '',
                'company'=>$faker->company(),
                'email'=>$faker->unique()->safeEmail(),
                'phone_number'=> $faker->numerify('#-####-##-#'),
                'hospital_name'=>$faker->city,
                'is_verified'=> true,
                'plan'=> 'exclusives',
                'name'=>$faker->name(),
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
    }
}

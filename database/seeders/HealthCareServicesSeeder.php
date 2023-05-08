<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class HealthCareServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        //
        $faker = Faker::create();
        DB::table('health_care_services')->insert([
            [
                'services' => 'Dental Check Up',
                'service_name' => 'dental',
                'appointment_id' => 1,
                'transaction_id'=> 1,
                'user_id'=> 2,
                'amount_paid' => 15000,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Peri-Apical',
                'service_name' => 'dental',
                'appointment_id' => 1,
                'transaction_id'=> 2,
                'user_id'=> 2,
                'amount_paid' => 4500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Occlusal',
                'service_name' => 'dental',
                'appointment_id' => 2,
                'transaction_id'=> 3,
                'user_id'=> 3,
                'amount_paid' => 4500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Biteview',
                'service_name' => 'dental',
                'appointment_id' => 3,
                'transaction_id'=> 4,
                'user_id'=> 4,
                'amount_paid' => 8500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Composite Fillings',
                'service_name' => 'dental',
                'appointment_id' => 3,
                'transaction_id'=> 5,
                'user_id'=> 4,
                'amount_paid' => 8500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Scaling and Polishing',
                'service_name' => 'dental',
                'appointment_id' => 4,
                'transaction_id'=> 6,
                'user_id'=> 5,
                'amount_paid' => 12500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Temporary Fillings',
                'service_name' => 'dental',
                'appointment_id' => 5,
                'transaction_id'=> 7,
                'user_id'=> 6,
                'amount_paid' => 4000,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Desensitization',
                'service_name' => 'dental',
                'appointment_id' => 6,
                'transaction_id'=> 8,
                'user_id'=> 7,
                'amount_paid' => 7000,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Quadrant/Subgingival, Scaling/Curettage',
                'service_name' => 'dental',
                'appointment_id' => 7,
                'transaction_id'=> 9,
                'user_id'=> 8,
                'amount_paid' => 54000,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Root canal therapy (pulpectomy)',
                'service_name' => 'dental',
                'appointment_id' => 8,
                'transaction_id'=> 10,
                'user_id'=> 9,
                'amount_paid' => 62500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Gingivectomy',
                'service_name' => 'dental',
                'appointment_id' => 9,
                'transaction_id'=> 11,
                'user_id'=> 10,
                'amount_paid' => 13500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Operculectomy',
                'service_name' => 'dental',
                'appointment_id' => 9,
                'transaction_id'=> 12,
                'user_id'=> 10,
                'amount_paid' => 13500,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Minor surgical extraction (non-impacted)',
                'service_name' => 'dental',
                'appointment_id' => 10,
                'transaction_id'=> 13,
                'user_id'=> 2,
                'amount_paid' => 43750,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Impacted third molar surgery',
                'service_name' => 'dental',
                'appointment_id' => 11,
                'transaction_id'=> 14,
                'user_id'=> 3,
                'amount_paid' => 43750,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'services' => 'Repositioning & Splinting',
                'service_name' => 'dental',
                'appointment_id' => 12,
                'transaction_id'=> 15,
                'user_id'=> 4,
                'amount_paid' => 43750,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],            
        ]);
    }
}

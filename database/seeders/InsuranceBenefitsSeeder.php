<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

function benefitsFormatter($name, $type, $value, $sex)
{
    /**
     * type: single|family|mixed(single & family),
     * sex: female|mixed(male & female)
     */

    return [
        'name' => $name,
        'type' => $type,
        'value' => json_encode($value),
        'sex' => $sex,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
}



class InsuranceBenefitsSeeder extends Seeder
{
    // constants
    private $male = 'male';
    private $female = 'female';
    private $mixed = 'mixed';
    private $single = 'single';
    private $family = 'family';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('insurance_benefits')->insert([
            benefitsFormatter('Ambulance Service', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 2], $this->mixed),
            // benefitsFormatter('Type of Care', $this->mixed, ['Select' => 0, 'Primary' => 5, 'Secondary' => 10], $this->mixed),
            // benefitsFormatter('Specialist Consult', $this->mixed, ['Dermatologist' => 10, 'Orthopedic-Surgeon' => 10, 'Cardiologist' => 10, 'Neurologist' => 10, 'Pediatrician' => 10, 'Endocrinologist' => 10, 'Gynecologist' => 10], $this->mixed),
            benefitsFormatter('Dermatologist', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Orthopedic-Surgeon', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Cardiologist', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Neurologist', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Pediatrician', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Endocrinologist', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            benefitsFormatter('Gynecologist', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            
            benefitsFormatter('Diagnostics', $this->mixed, ['Select' => 0, 'Primary' => 10, 'Secondary' => 30, 'Advanced' => 50], $this->mixed),
            benefitsFormatter('Immunization', $this->mixed, ['Select' => 0, 'No' => 0, '0-5 years' => 5, 'Adult' => 8], $this->mixed),
            benefitsFormatter('Maternity Care', $this->mixed, ['Select' => 0, 'No' => 0, 'Ante-Natal' => 5, 'NormalDelivery' => 6, 'Ante-Natal+NormalDelivery' => 10, 'Ante-Natal+NormalDelivery+PostNatal' => 12, 'NormalDelivery+PostNatal' => 8, 'Ante-Natal+PostNatal' => 8, 'PostNatal' => 3], $this->female),
            benefitsFormatter('Neo-natal Service', $this->mixed, ['Select' => 0, 'No' => 0, 'Up-to 50k' => 5, 'Up-to 75k' => 7, 'Up-to 100k' => 10], $this->female),
            benefitsFormatter('Surgical Service', $this->mixed, ['Select' => 0, 'No' => 0, 'Up-to 150k' => 5, 'Up-to 200k' => 7, 'Up-to 500k' => 15, 'Up-to 700k' => 25], $this->mixed),
            benefitsFormatter('Optical Care', $this->mixed, ['Select' => 0, 'Primary' => 2, 'Secondary' => 5], $this->mixed),
            benefitsFormatter('Optical lens & frame limit', $this->mixed, ['Select' => 0, 'No' => 0, '15k-20k' => 3, '20k-40k' => '5', '40k-60k' => 8], $this->mixed),
            benefitsFormatter('Dental Care', $this->mixed, ['Select' => 0, 'Primary' => 2, 'Secondary' => 5], $this->mixed),
            benefitsFormatter('Dental Care Limit', $this->mixed, ['Select' => 0, 'No' => 0, '15k-20k' => 3, '20k-40k' => 5, '40k-60k' => 8], $this->mixed),
            benefitsFormatter('Admission Ward', $this->mixed, ['Select' => 0, 'Standard' => 3, 'Semi-Private' => 8, 'Private' => 15], $this->mixed),
            benefitsFormatter('Annual Health Check', $this->mixed, ['Select' => 0, 'No' => 0, 'Basic' => 3, 'Comprehensive' => 10], $this->mixed),
            benefitsFormatter('Family Planning', $this->mixed, ['Select' => 0, 'No' => 0, 'Oral-Contraceptive' => 2, 'Injectables' => 5, 'Implant' => 6, 'IUCD' => 6], $this->mixed),
            benefitsFormatter('Fertility Services', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 5], $this->mixed),
            benefitsFormatter('Fertility Test', $this->mixed, ['Select' => 0, 'No' => 0, 'Up to 50k' => 5, 'Up to 100k' => 10], $this->mixed)
        ]);
    }
}
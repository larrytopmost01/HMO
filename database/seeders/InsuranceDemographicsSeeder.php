<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

function demographicsFormatter($name, $type, $value, $sex)
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

class InsuranceDemographicsSeeder extends Seeder
{
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
        DB::table('insurance_demographics')->insert([
            demographicsFormatter('Age Range', $this->single, ['Select' => 0, '18-29' => 4, '30-44' => 7, '45-60' => 10], $this->mixed),

            demographicsFormatter('Existing Illness', $this->mixed, ['Select' => 0,'No' => 0, 'Yes' => 10], $this->mixed),
            demographicsFormatter('Are you currently on medication?', $this->mixed, ['Select' => 0, 'No' => 5, 'Yes' => 10], $this->mixed),
            // demographicsFormatter('Choice of Hospital', $this->mixed, ['Guard' => 2, 'Guard 1' => 3, 'Guard 2' => 4, 'Shield' => 5, 'Shield 1' => 6, 'Premium' => 7, 'Premium 1' => 8, 'Premium 2' => 9, 'Exclusive' => 20], $this->mixed),
            demographicsFormatter('Are you pregnant?', $this->mixed, ['Select' => 0, 'No' => 0, 'Yes' => 20], $this->female),

            demographicsFormatter('Principal Age Range', $this->family, ['Select' => 0, '18-29' => 4, '30-44' => 7, '45-60' => 10], $this->mixed),
            // demographicsFormatter('Spouse', $this->family, ['Male' => 0, 'Female' => 0], $this->mixed),
            demographicsFormatter('Spouse Age Range', $this->family, ['Select' => 0, '18-29' => 4, '30-44' => 7, '45-60' => 10], $this->mixed),
            demographicsFormatter('Is spouse currently on medication?', $this->family, ['Select' => 0, 'No' => 5, 'Yes' => 10], $this->mixed),
            demographicsFormatter('Does spouse have existing illness', $this->family, ['Select' => 0, 'No' => 0, 'Yes' => 10], $this->mixed),
            demographicsFormatter('Is spouse pregnant?', $this->family, ['Select' => 0, 'No' => 0, 'Yes' => 20], $this->mixed),
            demographicsFormatter('How many dependant?', $this->family, ['Select' => 0, '0' => 0, '1' => 5, '2' => 10, '3' => 15, '4' => 20], $this->mixed) // this row should always be the last row in this table. New additions should be made above this row/line

        ]);
    }
}
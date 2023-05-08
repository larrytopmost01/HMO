<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

function opticalCareFormatter($name, $value, $type)
{
    /**
     * type: dental|optical
     */
    return [
        'name' => $name,
        'value' => json_encode($value),
        'type' => $type,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
}

class OpticalCareSeeder extends Seeder
{
    private $type = 'optical';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dental_optical_cares')->insert([
            opticalCareFormatter('Eye Check', ['No' => 0, 'Yes' => 20000, 
            'Sub-Service' => [
                'Consultation/Examination', 
                'Visual Acuity', 
                'Refraction',
                'Tonometry',
                'Central Visual Field',
                'Slit Lamp Examination',
                'Pachymetry',
                'Intraocular Pressure'
                ]], 
                $this->type),
            opticalCareFormatter('Dilation', ['No' => 0, 'Yes' => 2000], $this->type),
            opticalCareFormatter('Slit Lamp Examination (gonioscopy/tonometry)', ['No' => 0, 'Yes' => 4000], $this->type),
            opticalCareFormatter('Refraction', ['No' => 0, 'Yes' => 2500], $this->type),
            opticalCareFormatter('Central Visual Field (CVF)', ['No' => 0, 'Yes' => 6500], $this->type),
            opticalCareFormatter('Indirect Ophthalmoscopy', ['No' => 0, 'Yes' => 6500], $this->type),
            opticalCareFormatter('Pachymetry', ['No' => 0, 'Yes' => 4000], $this->type),
            opticalCareFormatter('Tonometry', ['No' => 0, 'Yes' => 5000], $this->type),
            opticalCareFormatter('Intraoccular Pressure (I.O.P)', ['No' => 0, 'Yes' => 4000], $this->type),
        ]);            
    }
}

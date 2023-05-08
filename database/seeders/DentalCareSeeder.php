<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

function dentalCareFormatter($name, $value, $type)
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

class DentalCareSeeder extends Seeder
{
    private $type = 'dental';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('dental_optical_cares')->insert([
            dentalCareFormatter('Dental Check Up', ['No' => 0, 'Yes' => 15000, 'Sub-Service' => ['Consultation/Examination', 'Scaling and Polishing', 'Occlusal Xrays (twice)']], $this->type),
            dentalCareFormatter('Peri-Apical', ['No' => 0, 'Yes' => 4500], $this->type),
            dentalCareFormatter('Occlusal', ['No' => 0, 'Yes' => 4500], $this->type),
            dentalCareFormatter('Biteview', ['No' => 0, 'Yes' => 4500], $this->type),
            dentalCareFormatter('Temporary Fillings', ['No' => 0, 'Yes' => 4000], $this->type),
            dentalCareFormatter('Composite Fillings', ['No' => 0, 'Yes' => 8500], $this->type),
            dentalCareFormatter('Scaling and Polishing', ['No' => 0, 'Yes' => 12500], $this->type),
            dentalCareFormatter('Desensitization', ['No' => 0, 'Yes' => 7000], $this->type),
            dentalCareFormatter('Quadrant/Subgingival, Scaling/Curettage', ['No' => 0, '1' => 13500, '2' => 2700, '3' => 40500, '4' => 54000], $this->type),
            dentalCareFormatter('Root canal therapy (pulpectomy)', ['No' => 0, 'Yes' => 62500], $this->type),
            dentalCareFormatter('Gingivectomy', ['No' => 0, 'Yes' => 13500], $this->type),
            dentalCareFormatter('Operculectomy', ['No' => 0, 'Yes' => 13500], $this->type),
            dentalCareFormatter('Minor surgical extraction (non-impacted)', ['No' => 0, 'Yes' => 13500], $this->type),
            dentalCareFormatter('Impacted third molar surgery', ['No' => 0, 'Yes' => 43750], $this->type),
            dentalCareFormatter('Repositioning & Splinting', ['No' => 0, 'Yes' => 43750], $this->type)
        ]);            
    }
}


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CancerScreeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $male = 'male';
    private $female = 'female';
    private $mixed = 'mixed';

    public function run()
    {
        DB::table('cancer_screenings')->insert([
            [
                'name' => 'Alpha-fetoprotein (AFP)',
                'description' => 'Liver, germ cell cell cancer of ovaries or testes',
                'sample' => 'Blood',
                'sex' => $this->mixed,
            ],
            [
                'name' => 'CA 15-3 (Cancer antigen 15-3)',
                'description' => 'Breast cancer and others, including lung, ovarian',
                'sample' => 'Blood',
                'sex' => $this->mixed,
            ],
            [
                'name' => 'CA 19-9 (Cancer antigen 19-9)',
                'description' => 'Pancreatic, sometimes bowel and bile ducts',
                'sample' => 'Blood',
                'sex' => $this->mixed,
            ],
            [
                'name' => 'CA-125 (Cancer antigen 125)',
                'description' => 'Ovarian',
                'sample' => 'Blood',
                'sex' => $this->female,
            ],
            [
                'name' => 'Carcinoembryonic antigen (CEA)',
                'description' => 'bowel, lung, breast, thyroid, pancreatic, liver, cervix and bladder',
                'sample' => 'Blood',
                'sex' => $this->mixed,
            ],
            [
                'name' => 'FOB (Fecal Occult Blood)',
                'description' => 'Screens for bleeding from the bowel which can indicate bowel cancers',
                'sample' => 'Stool',
                'sex' => $this->mixed,
            ],
        ]);
    }
}

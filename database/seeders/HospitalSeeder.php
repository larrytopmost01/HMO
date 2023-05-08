<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\HospitalImport;
use Maatwebsite\Excel\Facades\Excel;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new HospitalImport, 'database/files/hospitals.xlsx');
    }
}

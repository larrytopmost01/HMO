<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Imports\ProvidersImport;
use Maatwebsite\Excel\Facades\Excel;

class HealthServiceProvidersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Excel::import(new ProvidersImport, 'database/files/providers.xlsx');
    }
}

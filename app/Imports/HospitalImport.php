<?php

namespace App\Imports;

use App\Models\Hospital;
use Maatwebsite\Excel\Concerns\ToModel;

class HospitalImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $column)
    {
        return new Hospital([
            'name' => $column[0],
            'address' => $column[1],
            'location' => $column[2],
            'plan' => $column[3],
            'level' => (int) $column[4]
        ]);
    }
}
<?php

namespace App\Imports;

use App\Models\HealthServiceProviders;
use Maatwebsite\Excel\Concerns\ToModel;

class ProvidersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $column)
    {
        return new HealthServiceProviders([
            'location' => $column[0],
            'name' => $column[1],
            'address' => $column[2],
            'service_id' => $column[3],
        ]);
    }
}
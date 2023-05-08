<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance_Demographics extends Model
{
    use HasFactory;
    protected $table = 'insurance_demographics';
    protected $fillable = ['name', 'type', 'sex', 'value'];
    protected $casts = [
        'value' => 'array'
    ];
}

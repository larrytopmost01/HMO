<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceBenefits extends Model
{
    use HasFactory;

    protected $table = 'insurance_benefits';
    protected $fillable = ['name', 'type', 'sex', 'value'];
    protected $casts = [
        'value' => 'array'
    ];
}
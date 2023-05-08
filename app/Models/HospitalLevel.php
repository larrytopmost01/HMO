<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalLevel extends Model
{
    use HasFactory;

    protected $table = 'hospital_levels';
    protected $fillable = ['name', 'level', 'point'];
}
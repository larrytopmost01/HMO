<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancerScreening extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'sample', 'sex'];
}


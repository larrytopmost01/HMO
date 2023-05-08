<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DependentRequest extends Model
{
    use HasFactory;
    protected $table = 'dependent_requests';
    protected $fillable = ['request_id', 'request_type', 'principal_code', 'dependent_code'];
}

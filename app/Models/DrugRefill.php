<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugRefill extends Model
{
    use HasFactory;

    protected $table = 'drug_refills';
    protected $fillable = ['user_id', 'reason', 'drug_name', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 
        'code', 
        'discount_percent', 
        'dicounted_amount_ngn', 
        'amount_paid_ngn', 
        'cos_ngn',// cost of service nigerian naira
        'service_name',
        'is_used',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolleeRequestCard extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'card_collected', 'enrollee_id', 'passport_url', 'transaction_id', 'payment_amount', 'payment_type', 'payment_name', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

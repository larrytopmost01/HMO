<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'payment_id', 'payment_amount', 'transaction_id'];

    public function payments(){
        return $this->belongsTo(Payment::class);
    }
    public function users(){
        return $this->belongsTo(User::class);
    }
}

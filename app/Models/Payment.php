<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['payment_type', 'payment_name'];

    public function transactions(){
        return $this->hasOne(Transaction::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory;

    protected $table = 'subscription_history';
    protected $fillable = ['health_insurance_id', 'start_date', 'end_date', 'transaction_id', 'amount_paid', 'hospital'];

    public function health_insurance()
    {
        return $this->belongsTo(HealthInsurance::class);
    }
}
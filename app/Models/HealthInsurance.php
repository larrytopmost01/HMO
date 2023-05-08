<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthInsurance extends Model
{
    use HasFactory;

    protected $table = 'health_insurance';
    protected $fillable = ['user_id', 'type', 'sex', 'benefits', 'demographics'];

    public function subscription_history()
    {
        return $this->hasOne(SubscriptionHistory::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
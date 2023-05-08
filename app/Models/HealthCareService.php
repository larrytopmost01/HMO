<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCareService extends Model
{
    //use HasFactory;
    protected $fillable =   [ 'services', 
                              'service_name',
                              'appointment_id', 
                              'transaction_id', 
                              'user_id',
                              'amount_paid'
                            ];
   public function user()
   {
       return $this->belongsTo(User::class);
   }
   public function health_care_appointment()
   {
       return $this->hasOne(HealthCareAppointment::class);
   }
}   
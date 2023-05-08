<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthCareAppointment extends Model
{
    // use HasFactory;
    protected $fillable = [ 'user_id', 
                            'service_name', 
                            'comment', 
                            'doctor_name', 
                            'hospital_name', 
                            'hospital_location',
                            'hospital_address',
                            'appointment_time', 
                            'appointment_date',
                            'status'
                         ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function health_care_service()
    {
        return $this->belongsTo(HealthCareService::class);
    }
}

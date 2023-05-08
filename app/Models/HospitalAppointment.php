<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalAppointment extends Model
{
    use HasFactory;

    protected $table = 'hospital_appointments';
    protected $fillable = ['user_id', 'hospital_name', 'doctor_name', 'appointment_date', 'comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
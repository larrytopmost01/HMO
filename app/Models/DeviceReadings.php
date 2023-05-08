<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceReadings extends Model
{
    use HasFactory;

    protected $table = 'device_readings';
    protected $fillable = ['user_id', 'blood_pressure_readings', 'weight_readings', 'pulse_readings', 'temperature_readings', 'blood_sugar_readings'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'first_name', 'last_name', 'email', 'phone_number', 'is_verified', 'is_blocked', 'password',
    ];
    public function promo()
    {
        return $this->hasOne(Promo::class);
    }
    public function health_care_appointment()
    {
        return $this->hasMany(HealthCareAppointment::class);
    }
    public function health_care_service()
    {
        return $this->hasMany(HealthCareService::class);
    }
    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function device_readings(){
        return $this->hasMany(DeviceReadings::class);
    }

    public function otpcode()
    {
        return $this->hasOne(OtpCode::class);
    }

    public function resetcode()
    {
        return $this->hasOne(ResetCode::class);
    }

    public function enrollee()
    {
        return $this->hasOne(Enrollee::class);
    }

    public function hospital_appointments(){
        return $this->hasMany(HospitalAppointment::class);
    }

    public function drug_refills(){
        return $this->hasMany(DrugRefill::class);
    }

    public function subscription(){
        return $this->hasOne(Subscription::class);
    }

    public function health_insurance(){
        return $this->hasMany(HealthInsurance::class);
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    private function emailIsTaken($user)
    {

    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function requestCode(){
        return $this->hasMany(EnrolleeRequestCode::class);
    }
    public function requestCard(){
        return $this->hasMany(EnrolleeRequestCard::class);
    }
    public function tansactions(){
        return $this->hasMany(Transaction::class);
    }
    public function comment(){
        return $this->hasMany(Comment::class);
    }
}
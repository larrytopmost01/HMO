<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollee extends Model
{

        /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                    'user_id',
                    'enrollee_id',  
                    'company',
                    'email',
                    'phone_number',
                    'hospital_name',
                    'is_verified',
                    'plan',
                    'name'
                ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnrolleeRequestCode extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'hospital_name', 'request_message', 'enrollee_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

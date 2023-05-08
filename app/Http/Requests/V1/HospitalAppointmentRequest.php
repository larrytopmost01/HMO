<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class HospitalAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hospital_name' => 'required|string',
            'doctor_name' => 'string|nullable',
            'appointment_date' => 'required|date|unique:hospital_appointments,appointment_date',
            'comment' => 'string|nullable'
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(){
        return [
            'hospital_name.required' => 'hospital_name is required',
            'doctor_name.string' => 'doctor_name should be a string',
            'appointment_date.required' => 'appointment_date is required',
            'comment.string' => 'comment should be a string'
        ];
    }
}
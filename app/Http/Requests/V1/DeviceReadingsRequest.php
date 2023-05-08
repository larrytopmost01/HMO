<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class DeviceReadingsRequest extends FormRequest
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
            'blood_pressure_readings' => 'required',
            'weight_readings' => 'required|string',
            'pulse_readings' => 'required',
            'temperature_readings' => 'required|string',
            'blood_sugar_readings' => 'required'
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(){
        return [
            'blood_pressure_readings.required' => 'blood_pressure_readings is required',
            'weight_readings.required' => 'weight_readings is reqired',
            'pulse_readings.required' => 'pulse_readings is required',
            'temperature_readings.required' => 'temperature_readings is required',
            'blood_sugar_readings.required' => 'blood_sugar_readings is required'
        ];
    }
}
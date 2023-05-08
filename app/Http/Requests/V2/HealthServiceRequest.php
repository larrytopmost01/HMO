<?php

namespace App\Http\Requests\V2;

use Illuminate\Foundation\Http\FormRequest;

class HealthServiceRequest extends FormRequest
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
            'services' => 'required',
            'transaction_id' => 'required|string|unique:health_care_services,transaction_id',
            'amount_paid' => 'required|numeric',
            'hospital_name' => 'required|string',
            'hospital_location' => 'required|string',
            'hospital_address' => 'required|string',
            'appointment_date' => 'required',
        ];
    }
}

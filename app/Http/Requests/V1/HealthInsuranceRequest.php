<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class HealthInsuranceRequest extends FormRequest
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
            'type' => 'required|string',
            'sex' => 'required|string',
            'benefits' => 'required',
            'demographics' => 'required',
            'transaction_id' => 'required|string|unique:subscription_history,transaction_id',
            'amount_paid' => 'required|numeric',
            'hospital' => 'required|string'
        ];
    }
}
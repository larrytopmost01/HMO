<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EnrolleeCardRequest extends FormRequest
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
            'card_collected'=>'required|boolean',
            'passport_url'=>'nullable|string',
            'payment_type'=>'string',
            'payment_name'=>'string',
            'payment_amount'=>'integer',
            'transaction_id'=>'string',
            'address'=>'string'
          ];
    }
    public function messages(){
        return [
            'card_collected'=>'card_collected field is required and should be a boolean value',
            'passport_url'=>'passport_url should be a url string',
            'payment_type'=>'payment type should be a string',
            'payment_name'=>'payment name should be a string',
            'payment_amount'=>'payment amount should be a number',
            'transaction_id'=>'transaction id should be a valid string',
            'address'=>'address is required'
        ];
    }
}

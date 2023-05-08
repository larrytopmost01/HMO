<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EnrolleeCodeRequest extends FormRequest
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
            'hospital_name'=>'required|string',
          'request_message'=>'required|string',
          ];
    }
    public function messages(){
        return [
            'request_message'=>'request message is a required field',
            'hospital_name'=>'hospital name is a required field',
            
            
        ];
    }
}

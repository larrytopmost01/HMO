<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
   'first_name'     => 'required|string|max:50',
   'last_name'      => 'required|string|max:50',
   //make email field unique
   'email'          => 'required|email|unique:users,email',
   //make phone_number field unique
   'phone_number'   => 'required|string|max:14|unique:users,phone_number',
   'password'       => 'required|min:6',
  ];
 }

 /**
  * Custom error messages
  */

 public function messages()
 {
  return [
   'email.required'      => 'Email is required!',
   'first_name.required' => 'First name is required!',
   'last_name.required'  => 'Last name is required!',
   'phone_number.required'  => 'Phone number is required!',
   'password.required'   => 'Password is required!',
   'password.min'        => 'Password lenght is too short, minimum of six (6) characters!',
  ];
 }

}
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\EnrolleeCodeRequest;
use Illuminate\Http\Request;
use App\Models\EnrolleeRequestCode;
use App\Models\DependentRequest;
use JWTAuth;
use Auth;

class EnrolleeRequestCodeController extends Controller
{
    public function storeRequestCode(EnrolleeCodeRequest $request){
        $user = JWTAuth::user();
        $validatedData = $request->validated();


        $validatedData['user_id'] = $user['id'];

        $validatedData['enrollee_id'] = $user->enrollee->enrollee_id;
        $validatedData['hospital_name'] = json_encode($validatedData['hospital_name']);
        $validatedData['request_message'] = json_encode($validatedData['request_message']);

        $codeRequest = EnrolleeRequestCode::create($validatedData);

        //if $request has dependent_code then update dependent_request table
        if($request->dependent_code !== null){
            $dependent_request = DependentRequest::create([
                'request_id' => $codeRequest->id,
                'request_type' => 'code_request',
                'principal_code' => $user->enrollee->enrollee_id,
                'dependent_code' => $request->dependent_code
            ]);
            $dependent_request->save();
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'code request was successful',
            'data' => null
        ], 201);
       }
}

<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\V1\EnrolleeCardRequest;
use App\Utils\ResponseFormatter;
use App\Models\EnrolleeRequestCard;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\DependentRequest;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class EnrolleeRequestCardController extends Controller

{
    private function convertStringToBoolean($string){
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    private function paymentError($data){
            $error = FALSE;

            if(!isset($data['payment_name']) ||
                !isset($data['payment_type']) ||
                !isset($data['payment_amount'])
                ) $error = TRUE;

          return $error;
    }

    private function handleRequestCardPayment($validatedData, $user){

        if(isset($validatedData['transaction_id'])){
            $payment_type = $validatedData['payment_type'];
            $payment_name = $validatedData['payment_name'];

             $payment_id = Payment::create(['payment_type'=>$payment_type, 'payment_name'=>$payment_name])->id;
             $transaction = Transaction::create(['payment_amount'=>$validatedData['payment_amount'],
                                                    'payment_id'=>$payment_id,
                                                    'user_id'=>$user['id'],
                                                    'transaction_id'=>$validatedData['transaction_id']
                                                ]);
            return $transaction->id;

        }
    }




    public function storeCardRequest(EnrolleeCardRequest $request){
        $user = JWTAuth::user();

        $validatedData = $request->validated();

        $validatedData['user_id'] = $user['id'];
        $validatedData['enrollee_id'] = $user->enrollee->enrollee_id;
        $validatedData['card_collected'] = json_encode($validatedData['card_collected']);


        if(!$this->convertStringToBoolean($validatedData['card_collected']) && !isset($validatedData['passport_url'])){
            return response()->json(['status'=>FALSE, 'message'=>'passport Url is required'], 400);
        }

        if($this->convertStringToBoolean($validatedData['card_collected']) && !isset($validatedData['transaction_id'])){
            return response()->json(['status'=>FALSE, 'message'=>'payment details is required for enrollee who has collected card before'], 400);
        }

        if(isset($validatedData['transaction_id']) && !$this->convertStringToBoolean($validatedData['card_collected'])){
            return response()->json(['status'=>FALSE, 'message'=>'card collected field must be true'], 400);
        }

        if(isset($validatedData['transaction_id']) && $this->paymentError($validatedData)){
            return response()
                    ->json(['status'=>FALSE,'message'=>
                [
                    'payment_name'=>'is a required field',
                    'payment_type'=>'is a required field',
                    'payment_amount'=>'is a required field',
                    'passport_url'=>'is a required field'
                ],

                ],
                     400);
        }

       $validatedData['transaction_id'] =  $this->handleRequestCardPayment($validatedData, $user);

       if(!isset($validatedData['passport_url'])){
           $validatedData['passport_url'] = null;
       }

        if($this->paymentError($validatedData)){
            $validatedData['transaction_id'] = null;
            $validatedData['passport_url'] = $validatedData['passport_url'];
        }
        $request_data = ['user_id'=>$user['id'], 'transaction_id'=>$validatedData['transaction_id'],
                        'enrollee_id'=>$validatedData['enrollee_id'], 'card_collected'=>$validatedData['card_collected'],
                        'passport_url'=>$validatedData['passport_url'], 'status' => 'pending'
                    ];
        $cardRequested = EnrolleeRequestCard::create($request_data);
        if($request->dependent_code !== null){
            $dependent_request = DependentRequest::create([
                'request_id' => $cardRequested->id,
                'request_type' => 'card_request',
                'principal_code' => $user->enrollee->enrollee_id,
                'dependent_code' => $request->dependent_code
            ]);
            $dependent_request->save();
        }

        return response()->json([
            'status' => TRUE,
            'message' => 'card request was successful',
            'data' => null
        ], 201);
    }

}

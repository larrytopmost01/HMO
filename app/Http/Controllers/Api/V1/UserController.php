<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\UserStoreRequest;
use App\Http\Resources\V1\User\UserResource;
use App\Http\Controllers\Api\V1\Controller;
use App\Utils\ShortCodeGenerator as ShortCode;
use App\Utils\PromoCodeGenerator as PromoCode;
use App\Utils\EmailRegexValidator as Validator;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Mailer;
use App\Models\User;
use App\Models\Role;
use App\Models\OtpCode;
use App\Models\Promo;
use App\Utils\Jobs;
use Hash;
use JWTAuth;

class UserController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerUser(UserStoreRequest $request, Promo $promo){
      if(Validator::isEmail($request->email) !== true) return response()->json(ResponseFormatter::errorResponse('The email is incorrect'), 400);
      $user = User::create($request->validated());
      $user->password = Hash::make($user->password);
      //override the default value of is_verified from true to false
      if($request->client && $request->client == 'web'){
        $user->is_verified = true;
      }else{
        $user->is_verified = false;
      }
      $user->save();
      $token = JWTAuth::fromUser($user);
      //DON'T UNCOMMENT THIS LINE!
    //   $promo->code = PromoCode::generatePromoCode();
    //   $promo->discount_percent = random_int(5, 10);
    //   $promo = $user->promo()->save($promo);
      $response = response()->json([
                      'status'     => true,
                      'message'    => 'Registration successful',
                      'data'       => [
                          'user'       => $user,
                          'token'      => 'Bearer' . " " . $token,
                          'token_type' => 'Bearer',
                      ]  
                  ], Response::HTTP_CREATED);
      return $response;
    }

    public function getPromo()
    {
        $user = JWTAuth::user();
        $promo = $user->load('promo')->promo;
        if(is_null($promo)){
            $promo = new Promo;
            $promo->code = PromoCode::generatePromoCode();
            $promo->discount_percent = random_int(5, 10);
            $promo = $user->promo()->save($promo);
        }
        $promo_code = (boolean)$promo->is_used === false ? $promo->code : null;
        $promo_discount_percent = (boolean)$promo->is_used === false ? $promo->discount_percent : null;
        $data = [
            'code' => $promo_code,
            'discount_percent' => $promo_discount_percent
        ];
        return response()->json([
            'status' => true,
            'message' => 'Promo retrieved successfully',
            'data' => $data
        ], Response::HTTP_OK);
    }
}
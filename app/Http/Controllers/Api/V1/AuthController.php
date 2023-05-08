<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Controller;
use App\Http\Resources\V1\User\UserResource;
use App\Utils\ShortCodeGenerator as ShortCode;
use App\Utils\EmailRegexValidator as Validator;
use App\Utils\ResponseFormatter;
use App\Utils\HttpResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\OtpCode;
use App\Models\ResetCode;
use App\Utils\Jobs;
use JWTAuth;
use Hash;

class AuthController extends Controller
{
    /**
     * Confirm mobile number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function confirmMobileNumber(Request $request)
    {
        $user = User::where('phone_number', $request->phone_number)->first();
        return isset($user)
        ? response()->json(ResponseFormatter::successResponse('Confirmation successful', ['first_name' => $user->first_name]), Response::HTTP_OK)
        : response()->json(ResponseFormatter::errorResponse('Confirmation failed, user not found!', null), Response::HTTP_NOT_FOUND);
    }


    /**
     * Attempt login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'phone_number', 'password');
        try {
         if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status'     => false,
                'error'       => 'Invalid credentials',
                'message'     => 'Login failed',
            ], 400);
         }
        } catch (JWTException $e) {
         return response()->json(['error' => 'could_not_create_token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'status'     => true,
            'is_verified' => JWTAuth::user()->is_verified,
            'message'    => 'Login was successful',
            'payload'    => JWTAuth::user()->toArray(),
            'type'       => 'token',
            'token'      => 'Bearer' . ' ' . $token,
        ], Response::HTTP_OK);
    }


    public function getAuthenticatedUser()
    {
        if (!$user = JWTAuth::parseToken()->authenticate()) 
        return response()->json(['user_not_found'], Response::HTTP_NOT_FOUND );
        $enrollee = Enrollee::where('user_id', $user->id)->first();
        $response = response([
        'status'     => true,
        'message'     => 'Authenticated',
        'payload'        => [
            'user' => $user->toArray(),
            'enrollee' => $enrollee,
        ],
        ], Response::HTTP_OK);
        return $response;
    }

    public function sendOtpCode(Request $request)
    {
        $code = null;
        $otp = null;
        $to = null;
        if(Validator::isEmail($request->email) !== true) 
        return response()->json(ResponseFormatter::errorResponse('The email is incorrect'), 400);
        if($request->has('is_enrollee') && $request->is_enrollee == true)
        {
            $enrollee = Enrollee::where('email', $request->email)->first();
            if(!isset($enrollee)) return response()->json(ResponseFormatter::errorResponse('Enrollee not found'), 404);
            $user = User::find($enrollee->user_id);
            if(!$user) return response()->json(ResponseFormatter::errorResponse('User not found'), 404);
            $code = new ShortCode($user);
            $to = $request->email;
            $otp = $code->getOtpCode();
        }else{
            $user = User::where('email', $request->email)->first();
            if(!$user)
            return response()->json(ResponseFormatter::errorResponse('User not found'), 404);
            $code = new ShortCode($user);
            $to = $user->email;
            $otp = $code->getOtpCode(); 
        }
        $data = [
            'message' => 'Your OTP code is: '.$otp,
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'OTP Code',
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);
        $response = response()->json([
                        'status'     => true,
                        'otp_code'   => $otp,
                        'message'  => 'OTP code sent successfully!',
                    ], Response::HTTP_OK);
        return $response;
    }

    /**
     * Verify OtpCode.
     *
     * @param  \Illuminate\Http\Response
     * @return \Illuminate\Http\Response
     */
    public function verifyUserAccount(Request $request)
    {
        if((boolean) $request->status !== true) 
        return response()->json(ResponseFormatter::errorResponse('Verification failed, expected status to be true'), Response::HTTP_BAD_REQUEST);
        if(Validator::isEmail($request->email) !== true) 
        return response()->json(ResponseFormatter::errorResponse('Verification failed, the email is incorrect'), Response::HTTP_BAD_REQUEST);
        $user = User::where('email', $request->email)->first();
        if($user){
            $user->is_verified = (boolean) $request->status;
            $user->save();
                $response = response([
                    'status' => true,
                    'message' => 'Verification successful'
                ], Response::HTTP_OK);
                return $response;
        }else{
            $response = response()->json([
                'status' => false,
                'message' => 'Verification failed, user not found'
            ], Response::HTTP_NOT_FOUND);
            return $response;
        }
    }

    public function sendResetCode(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if($user == null) return response()->json([
            'status'     => false,
            'message'    => 'Email not found!'
        ], Response::HTTP_NOT_FOUND);

        $code = new ShortCode($user);
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Reset Password',
            'message' => 'Your password reset code is ' . ' ' . $code->getResetCode(),
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);
        $response = response()->json([
                        'status'     => true,
                        'message'  => 'Password reset code has been sent to' . ' ' . $user->email,
                    ], Response::HTTP_OK);
        return $response;
    }


    /**
     * Verify reset code
     *
     * @param  \Illuminate\Http\Request  $request and $id
     * @return \Illuminate\Http\Response
     */
    public function verifyResetCode(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        $reset = ResetCode::where('reset_code', $request->reset_code)->first();
        if($user !== null && $reset !== null) return response([
                                                'status' => true,
                                                'message' => 'Verification successful'
                                            ], Response::HTTP_OK);

        $response = response([
            'status' => false,
            'message' => 'Verification failed'
        ], Response::HTTP_BAD_REQUEST);
        return $response;
    }

    public function updatePassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $response = response()->json([
                'status'  => true,
                'message' => 'Password updated successfully!'
            ], Response::HTTP_OK);
            return $response;
    }
}
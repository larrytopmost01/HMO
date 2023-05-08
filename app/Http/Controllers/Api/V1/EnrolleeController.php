<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Enrollee;
use App\Models\Subscription;
use App\Models\HealthInsurance;
use App\Models\Promo;
use App\Utils\Proxy;
use App\Utils\HttpResponse;
use App\Utils\Message;
use App\Utils\SetDependantRecord;
use App\Utils\ResourceTransformer;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\V1\Enrollee\EnrolleeResource;
use JWTAuth;

class EnrolleeController extends Controller
{

    /**
     * Validate enrollee id
     * @param  $enrolleeId
     * @return enrollee
     */
    public function getEnrollee(Request $request)
    {
        //remove the last two characters if enrollee code has more than 3 forward slashes: input (GDM/21/340/W/G), output (GDM/21/340/W)
        $enrollee_id = substr_count($request->enrollee_id, '/') > 3 ? substr($request->enrollee_id, 0, -2) : $request->enrollee_id;
        $enrollee = Enrollee::where('enrollee_id', $enrollee_id)->first();
        if ($enrollee !== null) {
            if ($enrollee->is_verified == true && $enrollee->user_id == JWTAuth::user()->id) {
                $transformer = new ResourceTransformer();
                $transformer->transform($enrollee);
                $data = $transformer->transform($enrollee);
                $data['expiration_date'] = date('jS F Y', strtotime(JWTAuth::user()->subscription->end_date));
                $status = true;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_1'))->message, 
                            $data, 
                            Response::HTTP_OK))->getJsonResponse();
            }
            if ($enrollee->is_verified == true && $enrollee->user_id !== JWTAuth::user()->id) {
                $status = false;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_2'))->message, 
                            null,
                            Response::HTTP_FORBIDDEN))->getJsonResponse();
            }
            if ($enrollee->is_verified == false && $enrollee->user_id == JWTAuth::user()->id) {
                $transformer = new ResourceTransformer();
                $data = $transformer->transform($enrollee);
                $data['expiration_date'] = "";
                $status = true;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_1'))->message, 
                            $data, 
                            Response::HTTP_OK))->getJsonResponse();
            }
            if ($enrollee->is_verified == false && $enrollee->user_id !== JWTAuth::user()->id) {
                if (JWTAuth::user()->enrollee !== null) {
                    $transformer = new ResourceTransformer();
                    JWTAuth::user()->enrollee->update($transformer->transform($enrollee));
                    $data = Enrollee::find(JWTAuth::user()->enrollee->id);
                    $data['expiration_date'] = "";
                    $status = true;
                    return (new HttpResponse
                                ($status, 
                                (new Message('MSG_3'))->message, 
                                $data,
                                Response::HTTP_OK))->getJsonResponse();
                }
                $transformer = new ResourceTransformer();
                $new_enrollee = Enrollee::create($transformer->transform($enrollee));
                $new_enrollee->save();
                $data = $transformer->transform($enrollee);
                $data['expiration_date'] = "";
                $status = true;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_3'))->message, 
                            $data, 
                            Response::HTTP_OK))->getJsonResponse();
            }
        }
        $proxy = new Proxy();
        $response = $proxy->validateEnrollee($enrollee_id);
        if ($response->getStatusCode() == Response::HTTP_OK) {
            $body = json_decode($response->getBody()->getContents(), true);
            $body['data']['Phone'] = $body['data']['Phone'] == null || $body['data']['Phone'] == "" ? JWTAuth::user()->phone_number : $body['data']['Phone'];
            $body['data']['Email'] = $body['data']['Email'] == null || $body['data']['Email'] == "" ? JWTAuth::user()->email : $body['data']['Email'];
            if(strtoupper(substr($body['data']['Code'], -1)) !== 'A') {
                $record = new SetDependantRecord($body['data']['Code']);
                $body['data']['HCP Name'] = $record->getDependantMissingRecords();
            }
            $body['data']['Phone'] = strpos($body['data']['Phone'], '+234') === 0 ? substr($body['data']['Phone'], 4) : $body['data']['Phone'];
            $is_exist = JWTAuth::user()->enrollee;
            if ($is_exist !== null && $is_exist->enrollee_id !== $enrollee_id && $is_exist->is_verified == true) {
                $status = false;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_4'))->message,
                            null, 
                            Response::HTTP_FORBIDDEN))->getJsonResponse();
            } elseif ($is_exist !== null && $is_exist->enrollee_id !== $enrollee_id && $is_exist->is_verified == false) {
                $is_exist->enrollee_id = $body['data']['Code'];
                $is_exist->name = $body['data']['Name'];
                $is_exist->email = $body['data']['Email'];
                $is_exist->company = $body['data']['Company'];
                $is_exist->plan = $body['data']['Plan'];
                $is_exist->phone_number = $body['data']['Phone'];
                $is_exist->hospital_name =  $body['data']['HCP Name'];
                $is_exist->is_verified = false;
                $is_exist->save();
                $transformer = new ResourceTransformer();
                $data = $transformer->transform($is_exist);
                $data['expiration_date'] = "";
                $status = true;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_3'))->message, 
                            $data, 
                            Response::HTTP_OK))->getJsonResponse();
            } elseif ($is_exist === null) {
                $data = array();
                $enrollee = new Enrollee();
                $enrollee->enrollee_id = $body['data']['Code'];
                $enrollee->name = $body['data']['Name'];
                $enrollee->email = $body['data']['Email'];
                $enrollee->company = $body['data']['Company'];
                $enrollee->plan = $body['data']['Plan'];
                $enrollee->phone_number = $body['data']['Phone'];
                $enrollee->hospital_name =  $body['data']['HCP Name'];
                $enrollee->is_verified = false;
                $user = JWTAuth::parseToken()->authenticate();
                $enrollee = $user->enrollee()->save($enrollee);
                $transformer = new ResourceTransformer();
                $data = $transformer->transform($enrollee);
                $data['expiration_date'] = date('jS F Y', strtotime($body['data']['End Date']));
                $status = true;
                return (new HttpResponse
                            ($status, 
                            (new Message('MSG_1'))->message, 
                            $data, 
                            Response::HTTP_OK))->getJsonResponse();
            }
        } else {
            return (new HttpResponse
                        (false, 
                        (new Message('MSG_5'))->message, 
                        null, 
                        Response::HTTP_BAD_REQUEST))->getJsonResponse();
        }
    }

    public function verifyEnrollee(Request $request)
    {
        if ($request->status == true) {
            $enrollee = JWTAuth::user()->enrollee;
            if ($enrollee->is_verified) {
                return response()->json(ResponseFormatter::errorResponse((new Message('MSG_8'))->message), Response::HTTP_CONFLICT);
            }
            
            $enrollee->is_verified = true;
            $enrollee->save();
            $proxy = new Proxy();
            $response = $proxy->validateEnrollee($enrollee->enrollee_id);
            $body = json_decode($response->getBody()->getContents(), true);
            $start_date = $body['data']['Start Date'];
            $end_date = $body['data']['End Date'];
            $subscriptionIsActive = strtotime($end_date) > strtotime(date('Y-m-d'));
            $subscriptionStatus = $subscriptionIsActive ? 'active' : 'inactive';
            $userSubscription = Subscription::create([
                'user_id' => JWTAuth::user()->id,
                'plan_name' => strtolower($body['data']['Plan']),
                'status' => $subscriptionStatus,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
            $expiration_date = date('jS F Y', strtotime($end_date));
            $data = array();
            $data['expiration_date'] = $expiration_date;
            $userSubscription->save();
            return (new HttpResponse
                        (true, 
                        (new Message('MSG_6'))->message, 
                        $data, 
                        Response::HTTP_OK))->getJsonResponse();
        } else {
            return (new HttpResponse
                        (false, 
                        (new Message('MSG_7'))->message, 
                        null, 
                        Response::HTTP_BAD_REQUEST))->getJsonResponse();
        }
    }

    public function getEnrolleePlanBenefits()
    {
        $enrollee = JWTAuth::user()->enrollee;
        $user_id = JWTAuth::user()->id;
        $current_plan = $enrollee->plan;
        $client_code = substr($enrollee->enrollee_id, 0, 3);
        if($current_plan === 'custom'){
            $possible_health_insurance = HealthInsurance::where('user_id', $user_id)->orderByDesc('created_at')->first();
            if($possible_health_insurance === null){
                return response()->json(ResponseFormatter::errorResponse('health insurance not found'), 404);
            }

            $decoded_benefits = json_decode($possible_health_insurance->benefits);
            $plan_benefits = [];
            foreach($decoded_benefits as $key => $val){
                $benefit_string = $key . ' - ' . $val;
                array_push($plan_benefits, $benefit_string);
            }

            $response_object = [
                'plan_name' => $current_plan,
                'plan_benefits' => $plan_benefits,
            ];

            return response()->json(ResponseFormatter::successResponse('Benefits retrieved successfully', $response_object), 200);
        } 

        $proxy = new Proxy();
            $response = $proxy->getPlanBenefits(strtolower($current_plan), strtolower($client_code));
            if ($response->getStatusCode() == Response::HTTP_OK) {
                $benefits = (array) json_decode($response->getBody()->getContents(), true);
                $response_object = [
                    'plan_name' => strtoupper($enrollee->plan),
                    'plan_benefits' => $benefits['plan_benefits'],
                ];
                return response()->json(ResponseFormatter::successResponse('Benefits retrieved successfully', $response_object), 200);
            } 
            return (new HttpResponse
                            (false, 
                            (new Message('MSG_10'))->message, 
                            null, 
                            Response::HTTP_NOT_FOUND))->getJsonResponse();
        
    }



}
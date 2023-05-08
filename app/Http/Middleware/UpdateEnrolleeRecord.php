<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Enrollee;
use App\Models\Subscription;
use App\Utils\Proxy;
use App\Utils\SetDependantRecord;
use App\Utils\ResourceTransformer;
use App\Utils\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\V1\Enrollee\EnrolleeResource;
use JWTAuth;

class UpdateEnrolleeRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //remove the last two characters if enrollee code has more than 3 forward slashes: input (GDM/21/340/W/G), output (GDM/21/340/W)
        $enrollee_id = substr_count($request->enrollee_id, '/') > 3 ? substr($request->enrollee_id, 0, -2) : $request->enrollee_id;
        $enrollee = Enrollee::where('enrollee_id', $enrollee_id)->first();
        if($enrollee){
            $proxy = new Proxy();
            $response = $proxy->validateEnrollee($enrollee_id);
			// dd(JWTAuth::user()->id);
            if((boolean)$enrollee->is_verified === true && (int)$enrollee->user_id === JWTAuth::user()->id && $response->getStatusCode() === Response::HTTP_OK){
				$body = json_decode($response->getBody()->getContents(), true);
				$body['data']['Phone'] = $body['data']['Phone'] == null || $body['data']['Phone'] == "" ? JWTAuth::user()->phone_number : $body['data']['Phone'];
				$body['data']['Email'] = $body['data']['Email'] == null || $body['data']['Email'] == "" ? JWTAuth::user()->email : $body['data']['Email'];
				//if enrollee is not principal then set dependant record
				if(strtoupper(substr($body['data']['Code'], -1)) !== 'A') {
					$record = new SetDependantRecord($body['data']['Code']);
					$body['data']['HCP Name'] = $record->getDependantMissingRecords();
				}
				$body['data']['Phone'] = strpos($body['data']['Phone'], '+234') === 0 ? substr($body['data']['Phone'], 4) : $body['data']['Phone'];
				$enrollee->name = $body['data']['Name'];
                $enrollee->email = $body['data']['Email'];
                $enrollee->company = $body['data']['Company'];
                $enrollee->plan = $body['data']['Plan'] === '' ? 'custom' : $body['data']['Plan'];
                $enrollee->phone_number = $body['data']['Phone'];
                $enrollee->hospital_name =  $body['data']['HCP Name'];
				$enrollee->save();
                if($enrollee->plan !== 'custom'){
                    $subscription = Subscription::where('user_id', JWTAuth::user()->id)->first();
                    //set end date, start date, plan name and subscription status
                    $subscription->end_date = $body['data']['End Date'];
                    $subscription->start_date = $body['data']['Start Date'];
                    $subscription->plan_name = strtolower($enrollee->plan);
                    $subscription->status = strtolower($body['data']['Status']) === 'suspended' ? 'inactive' : 'active';
                    $subscription->save();
                }
            }
        }
        return $next($request);
    }
}

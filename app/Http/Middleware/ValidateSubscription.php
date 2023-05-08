<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JWTAuth;

class ValidateSubscription
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
        $subscription = JWTAuth::user()->load('subscription')->subscription;
        if(!is_null($subscription)){
            if($subscription->status != 'inactive'){
                return response()->json([
                    'status'=>false,
                    'message'=>'Oops!, you can\'t buy a plan at the moment, your subscription is either pending or active.',
                    'data'=>null
                ], Response::HTTP_OK);
            }     
        }
        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use JWTAuth;

class VerifyEnrollee
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
        $user = JWTAuth::user();
        if(!$user->enrollee || !$user->enrollee->enrollee_id){
            return response()->json([
                'status'=>FALSE,
                'message'=>'You are not an active enrollee',
                'data'=>null
            ], 403);
        }
        return $next($request);
    }
}

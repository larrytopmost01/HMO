<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\Message;
use App\Utils\HttpResponse;
use JWTAuth;

class CheckStatus
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
        $is_pending = $subscription->status === 'pending';
        $is_inactive = $is_pending === true ? true : strtotime($subscription->end_date) < strtotime(date('Y-m-d'));
        $new_subscription_status = $is_pending === true ? 'pending' : 'inactive';
        if ($is_inactive == true) {
            $subscription->status = $new_subscription_status;
            $subscription->save();

            $message_suffix = $new_subscription_status === 'pending' ? 'is pending' : 'has expired';
            $error_message = 'Oops!, your subscription ' . $message_suffix . ', please contact your HR';
            return (new HttpResponse(
                false,
                $error_message,
                null,
                Response::HTTP_OK
            ))->getJsonResponse();
        }
        return $next($request);
    }
}
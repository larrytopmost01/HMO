<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Requests\V2\HealthServiceRequest;
use JWTAuth;

class UpdatePromo
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
        $promo = JWTAuth::user()->load('promo')->promo;
        if($request->has('has_promo')) {
            $promo->is_used = $request->has_promo === true ? true : false;
            $promo->service_name = $request->service_name;
            //get cost of service
            $promo->cos_ngn = $request->amount_paid;
            //compute amount paid after discount
            $promo->amount_paid_ngn = $promo->cos_ngn - ($promo->cos_ngn * $promo->discount_percent / 100);
            //compute discounted amount
            $promo->dicounted_amount_ngn = $promo->cos_ngn - $promo->amount_paid_ngn;
            $promo->save();
        }
        return $next($request);
    }
}

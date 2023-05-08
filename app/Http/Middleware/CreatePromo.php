<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Utils\PromoCodeGenerator as PromoCode;
use App\Models\Promo;
use JWTAuth;

class CreatePromo
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
        $promo = new Promo();
        if(is_null(JWTAuth::user()->load('promo')->promo)) {
            $promo->code = PromoCode::generatePromoCode();
            $promo->discount_percent = random_int(5, 10);
            $promo = JWTAuth::user()->promo()->save($promo);
        }
        return $next($request);
    }
}

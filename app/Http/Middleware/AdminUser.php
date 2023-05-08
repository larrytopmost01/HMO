<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\Message;
use App\Utils\ResponseFormatter;
use App\Utils\HttpResponse;
use App\Models\Role;
use App\Models\User;
use JWTAuth;

class AdminUser
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
        //-----THIS IS MEANT TO RESTRICT ACCESS TO NON ADMIN USERS FROM LOGGING INTO ADMIN DASHBOARD-----
        //check if user is logged in
        if (JWTAuth::user() === null) {//if user is not logged in
                $userRole = Role::where('id', User::where('email', $request->email)->first()->role_id)->first();
                if($userRole->name != 'admin'){
                    return response()->json(ResponseFormatter::errorResponse('You are forbidden from viewing this resource'), 403);
                }
                return $next($request);
        }
        //-----THIS IS MEANT TO RESTRICT ACCESS TO NON ADMIN USERS FROM VIEWING ADMIN RESOURCES-----
        //check if loggedin user is admin
        $userRole = Role::where('id', JWTAuth::user()->role_id)->first();
        if($userRole->name != 'admin'){
            return response()->json(ResponseFormatter::errorResponse('You are forbidden from viewing this resource'), 403);
        }
        return $next($request);
    }
}
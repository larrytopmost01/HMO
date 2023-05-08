<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'error' => 'invalid_url',
                'error_description' => 'The requested URL was not found on this server.',
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json([
                'error' => 'model_not_found',
                'error_description' => 'The requested model could not be found.',
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (TokenExpiredException $e, $request) {
            return response()->json([
                'error' => 'expired_token',
                'error_description' => 'The token has expired.',
            ], Response::HTTP_UNAUTHORIZED);
        });

        $this->renderable(function (TokenInvalidException $e, $request) {
            return response()->json([
                'error' => 'invalid_token',
                'error_description' => 'The access token is invalid'
            ], Response::HTTP_UNAUTHORIZED);
        });

        $this->renderable(function (JWTException $e, $request) {
            return response()->json([
                'error' => 'token_absent',
                'error_description' => 'The access token was not present in the request.'
            ], Response::HTTP_UNAUTHORIZED);
        });

        //validation exception
        $this->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'message' => $e->validator->errors()->first(),
                'error' => 'validation_error',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });
    }
}

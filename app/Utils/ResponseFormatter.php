<?php

namespace App\Utils;

class ResponseFormatter
{
    public static function successResponse($message, $data = null)
    {
        return [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function errorResponse($errorMessage)
    {
        return [
            'status' => false,
            'message' => $errorMessage,
            'data' => null
        ];
    }
}
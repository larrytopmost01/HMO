<?php
namespace app\Utils;
use JWTAuth;

class ResourceTransformer
{
    public static function transform($resource)
    {
        $resource = self::transformEnrolleeResource($resource);
        return $resource;
    }

    private static function transformEnrolleeResource($resource)
    {
      if (strpos($resource->phone_number, '+234') === 0) {
        $resource->phone_number = substr($resource->phone_number, 4);
    }
      return [
        'id'               => $resource->id,
        'enrollee_id'      => $resource->enrollee_id,
        'user_id'          => JWTAuth::user()->id,
        'name'             => $resource->name,
        'email'            => $resource->email,
        'phone_number'     => $resource->phone_number,
        'company'          => $resource->company,
        'plan'             => $resource->plan,
        'hospital_name'  => $resource->hospital_name,
        'is_verified'      => $resource->is_verified,
       ];
    }
}
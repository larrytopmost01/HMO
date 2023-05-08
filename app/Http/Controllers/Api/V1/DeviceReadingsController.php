<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\DeviceReadingsRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\DeviceReadings;
use App\Models;
use JWTAuth;
use Auth;

class DeviceReadingsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function recordDeviceReadings(DeviceReadingsRequest $request)
    {
        $user = JWTAuth::user();

            $validatedData = $request->validated();
            $validatedData['user_id'] = $user['id'];

            $validatedData['blood_pressure_readings'] = json_encode($validatedData['blood_pressure_readings']);
            $validatedData['pulse_readings'] = json_encode($validatedData['pulse_readings']);
            $validatedData['blood_sugar_readings'] = json_encode($validatedData['blood_sugar_readings']);

            $deviceReadings = DeviceReadings::create($validatedData);

            $deviceReadings->save();

            return response()->json([
                'status' => 'success',
                'message' => 'device readings recorded successfully',
                'data' => null
            ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewDeviceReadings(Request $request)
    {
        $user = JWTAuth::user();
        $userId = $user['id'];

        $deviceReadings = DeviceReadings::where('user_id', $userId)->orderByDesc('created_at')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'device readings retrieved successfully',
            'data' => $deviceReadings
        ], 200);
    }
}
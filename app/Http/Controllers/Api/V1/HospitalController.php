<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Hospital;
use App\Models\HealthInsurance;
use App\Models\HospitalLevel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class HospitalController extends Controller
{

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function getAllLocationsForEnrollees()
    {
        $user_id = JWTAuth::user()->id;
        $enrollee = JWTAuth::user()->enrollee;
        $current_plan = strtolower($enrollee->plan);
        $locations = null;

        if ($current_plan === 'custom') {
            $possible_health_insurance = HealthInsurance::where('user_id', $user_id)->orderByDesc('created_at')->first();
            if ($possible_health_insurance === null) {
                return response()->json(ResponseFormatter::errorResponse('health insurance not found'), 404);
            }

            $demographics = (array) json_decode($possible_health_insurance['demographics']);
            $hospital_plan = strtolower($demographics['Choice of Hospital']);

            $hospital_level = HospitalLevel::where('name', $hospital_plan)->first();

            $locations = DB::table('hospitals')
                ->distinct()
                ->where('level', '<=', $hospital_level->level)
                ->orderBy('location')
                ->pluck('location');
        } else {
            $hospital_level = HospitalLevel::where('name', $current_plan)->first();
            $locations = DB::table('hospitals')
                ->distinct()
                ->where('level', '<=', $hospital_level->level)
                ->orderBy('location')
                ->pluck('location');
        }

        $response_data = ["count" => count($locations), "locations" => $locations];
        return response()->json(ResponseFormatter::successResponse('Locations retrieved successfully', $response_data), 200);
    }

    public function getHospitalsByLocationForEnrollees($location)
    {
        $user_id = JWTAuth::user()->id;
        $enrollee = JWTAuth::user()->enrollee;
        $current_plan = strtolower($enrollee->plan);
        $hospitals = null;

        if ($current_plan === 'custom') {
            $possible_health_insurance = HealthInsurance::where('user_id', $user_id)->orderByDesc('created_at')->first();
            if ($possible_health_insurance === null) {
                return response()->json(ResponseFormatter::errorResponse('health insurance not found'), 404);
            }

            $demographics = (array) json_decode($possible_health_insurance['demographics']);
            $hospital_plan = strtolower($demographics['Choice of Hospital']);

            $hospital_level = HospitalLevel::where('name', $hospital_plan)->first();

            $hospitals = DB::table('hospitals')
                ->select('*')
                ->where('location', '=', strtoupper($location))
                ->where('level', '<=', $hospital_level->level)
                ->get();
        } else {
            $hospital_level = HospitalLevel::where('name', $current_plan)->first();
            $hospitals = DB::table('hospitals')
                ->select('*')
                ->where('location', '=', strtoupper($location))
                ->where('level', '<=', $hospital_level->level)
                ->get();
        }


        if (count($hospitals) === 0) {
            return response()->json(ResponseFormatter::errorResponse('Hospitals not found within that location for your insurance plan'), 404);
        }

        $response_data = ["count" => count($hospitals), "locations" => $hospitals];
        return response()->json(ResponseFormatter::successResponse('Hospitals retrieved successfully', $response_data), 200);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function getAllLocationsForNonEnrollees()
    {
        $locations = Hospital::distinct()->orderBy('location')->pluck('location');

        $response_data = ["count" => count($locations), "locations" => $locations];
        return response()->json(ResponseFormatter::successResponse('Locations retrieved successfully', $response_data), 200);
    }

    public function getHospitalsByLocationForNonEnrollees($location)
    {
        $hospitals = DB::table('hospitals')
            ->join('hospital_levels', 'hospitals.plan', '=', 'hospital_levels.name')
            ->select(['hospitals.*', 'hospital_levels.point'])
            ->orderBy('hospitals.name')
            ->where('hospitals.location', strtoupper($location))
            ->get();

        if (count($hospitals) === 0) {
            return response()->json(ResponseFormatter::errorResponse('Hospitals not found within that location'), 404);
        }

        $response_data = ["count" => count($hospitals), "locations" => $hospitals];
        return response()->json(ResponseFormatter::successResponse('Hospitals retrieved successfully', $response_data), 200);
    }
}
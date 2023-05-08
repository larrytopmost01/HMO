<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Models\HealthServiceProviders;
use App\Models\Service;

class HealthCentreLocationController extends Controller
{
    public function getHealthCentreLocations($type)
    {
        if (!in_array($type, ['dental', 'optical', 'comprehensive', 'cancer'])){
            return response()->json(ResponseFormatter::errorResponse('type must be dental, optical or comprehensive'), 400);
        }
        $service_id = Service::where('name', $type)->first();
        $locations = HealthServiceProviders::where('service_id', $service_id->id)
                                            ->distinct()
                                            ->orderBy('location')
                                            ->pluck('location');
        $response_data = ["count" => count($locations), "locations" => $locations];
        return response()->json(ResponseFormatter::successResponse('Locations retrieved successfully', $response_data), 200);
    }

    public function getHealthCentresByLocation($type, $location)
    {
        if (!in_array($type, ['dental', 'optical', 'comprehensive', 'cancer'])){
            return response()->json(ResponseFormatter::errorResponse('type must be dental, optical or comprehensive'), 400);
        }
        $service_id = Service::where('name', $type)->first();
        $health_centres = HealthServiceProviders::where('service_id', $service_id->id)
                                            ->where('location', $location)
                                            ->distinct()
                                            ->orderBy('location')
                                            ->get();
        if($health_centres->isEmpty() || $health_centres == null){
            return response()->json(ResponseFormatter::errorResponse('We could not find health centres or hospitals within ' . $location), 404);
        }
        $response_data = ["count" => count($health_centres), "health_centres" => $health_centres];
        return response()->json(ResponseFormatter::successResponse('Health centres retrieved successfully', $response_data), 200);
    }      
}

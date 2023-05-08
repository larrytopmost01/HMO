<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Utils\ResponseFormatter;
use App\Utils\DataBaseQueryHandler;
use App\Http\Requests\V2\HealthServiceRequest;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use App\Models\ComprehensiveCheck;
use App\Models\ComprehensiveCheckCostCentre;
use Illuminate\Support\Facades\DB;
use App\Utils\Jobs;
use JWTAuth;

class ComprehensiveCheckController extends Controller
{
    public function getComprehensiveCheck(DataBaseQueryHandler $dbQueryHandler, $type)
    {
        if (!in_array($type, ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'])){
            return response()->json(ResponseFormatter::errorResponse('type must be: "mb", "mp", "wb" or "wp", "bc", "pe"'), 400);
        }
        $comprehensive_health_check = $dbQueryHandler->getComprehensiveCheckResult($type);
        $price = ComprehensiveCheckCostCentre::where('name', $type)->pluck('price');
        $data = [
            'screenings' => $comprehensive_health_check,
            'cost' => (integer) $price[0]
        ];
        return response()->json(ResponseFormatter::successResponse('comprehensive checks details retrieved successfully', $data), 200);
    }

    public function createComprehensiveHealthCheck(DataBaseQueryHandler $dbQueryHandler, HealthServiceRequest $request)
    {
        $types = [
            'mb' => 'men-basic',
            'mp' => 'men-plus',
            'wb' => 'women-basic',
            'wp' => 'women-plus',
            'bc' => 'basic',
            'pe' => 'pre-employment'
        ];
        $user = JWTAuth::user();
        $validatedData = $request->validated();
        $type = strtolower($validatedData['services']);
        if (!array_key_exists($type, $types)){
            return response()->json(ResponseFormatter::errorResponse('type must be: "mb", "mp", "wb" or "wp", "bc", "pe"'), 400);
        }
        $validatedData['user_id'] = $user['id'];
        $validatedData['comment'] = $request->comment;
        $validatedData['doctor_name'] = $request->doctor_name;
        $comprehensive_health_check = $dbQueryHandler->getComprehensiveCheckResult($type);
        $final_services = [];
        $screenings_title = $comprehensive_health_check['names'];
        $screenings = $comprehensive_health_check['values'];
        for ($i=0; $i <count($screenings_title) ; $i++) { 
            $final_services[$screenings_title[$i]] = $screenings[$i];
        }
        //create appointment and save it to db
        $appointment = HealthCareAppointment::create([
            'user_id' => $user['id'],
            'service_name' => $request->service_name !== 'comprehensive' ? $request->service_name : "$request->service_name ({$types[$type]})",
            'doctor_name' => $request->doctor_name === null ? '' : $request->doctor_name,
            'hospital_name' => $validatedData['hospital_name'],
            'appointment_date' => $validatedData['appointment_date'],
            'comment' => $request->comment === null ? '' : $request->comment,
            'hospital_location' => $validatedData['hospital_location'],
            'hospital_address' => $validatedData['hospital_address'],
        ]);
        $appointment->save();
        //create health care service and save it to db
        $health_service = new HealthCareService();
        $health_service->appointment_id = $appointment->id;
        $health_service->service_name = "$request->service_name ({$types[$type]})";
        $health_service->user_id = JWTAuth::user()->id;
        $health_service->amount_paid = $validatedData['amount_paid'];
        $health_service->transaction_id = $validatedData['transaction_id'];
        $health_service->services = json_encode($final_services);
        $health_service->save();
        $data = [
            'name' => JWTAuth::user()->first_name . ' ' . JWTAuth::user()->last_name,
            'subject' => 'Health Care Service',
            'message' => 'Hi ' . JWTAuth::user()->first_name . ', ' . 'your payment has been received and your appointment is currently pending'
        ];
        $jobs = new Jobs();
        $jobs->mailerJob(JWTAuth::user()->email, $data);
        return response()->json(ResponseFormatter::successResponse('Appointment created successfully'), 200);
    }
}

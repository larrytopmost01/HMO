<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Utils\ResponseFormatter;
use App\Utils\DataBaseQueryHandler;
use App\Http\Requests\V2\HealthServiceRequest;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use App\Models\CancerScreening;
use App\Models\ComprehensiveCheckCostCentre;
use Illuminate\Support\Facades\DB;
use App\Utils\Jobs;
use JWTAuth;

class CancerScreeningController extends Controller
{
    public function getCancerScreenings($type)
    {
        if (!in_array($type, ['csm', 'csw'])){
            return response()->json(ResponseFormatter::errorResponse('type must be: csm or csw'), 400);
        }
        $sex = $type === 'csm' ? 'male' : 'female';
        $cancer_screenings = DB::table('cancer_screenings')
                                ->select(['name', 'description', 'sample', 'sex'])
                                ->where(function ($query) use (&$sex) {
                                    $query->where('sex', '=', $sex)
                                        ->orWhere('sex', '=', 'mixed');
                                })
                                ->get();
        $price = ComprehensiveCheckCostCentre::where('name', $type)->pluck('price');
        $data = [
            'screenings' => $cancer_screenings,
            'cost' => (integer) $price[0]
        ];
        return response()->json(ResponseFormatter::successResponse('cancer screenings details retrieved successfully', $data), 200);
    }

    public function createCancerScreenings(HealthServiceRequest $request)
    {
        $user = JWTAuth::user();
        $validatedData = $request->validated();
        $type = strtolower($validatedData['services']);
        if (!in_array($type, ['csm', 'csw'])){
            return response()->json(ResponseFormatter::errorResponse('services must be csm or csw'), 400);
        }
        if (strtolower($request->service_name) !== 'cancer'){
            return response()->json(ResponseFormatter::errorResponse('service_name must be cancer'), 400);
        }
        $validatedData['user_id'] = $user['id'];
        $validatedData['comment'] = $request->comment;
        $validatedData['doctor_name'] = $request->doctor_name;
        $sex = $type === 'csm' ? 'male' : 'female';
        $services = DB::table('cancer_screenings')
                                ->where(function ($query) use (&$sex) {
                                    $query->where('sex', '=', $sex)
                                        ->orWhere('sex', '=', 'mixed');
                                })
                                ->pluck('name');
        //create appointment and save it to db
        $appointment = HealthCareAppointment::create([
            'user_id' => $user['id'],
            'service_name' => "$request->service_name ({$sex})",
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
        $health_service->service_name = "$request->service_name ({$sex})";
        $health_service->user_id = JWTAuth::user()->id;
        $health_service->amount_paid = $validatedData['amount_paid'];
        $health_service->transaction_id = $validatedData['transaction_id'];
        $health_service->services = json_encode($services->toArray());
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

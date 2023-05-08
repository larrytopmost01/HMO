<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use App\Models\DentalOpticalCare;
use App\Utils\ResponseFormatter;
use App\Utils\DataBaseQueryHandler;
use App\Http\Requests\V2\HealthServiceRequest;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use Illuminate\Support\Facades\DB;
use App\Utils\Jobs;
use JWTAuth;

class DentalOpticalCareController extends Controller
{
    public function getDentalOpticalPrimaryCare($type)
    {
        if (!in_array($type, ['dental', 'optical'])){
            return response()->json(ResponseFormatter::errorResponse('type must be dental or optical'), 400);
        }
        $dental_optical_primary_care = DentalOpticalCare::where('type', $type)->get();
        $sub_service = [];
        foreach ($dental_optical_primary_care as $item) {
            $item_value = json_decode($item->value);
            $names = [];
            $prices = [];
            foreach ($item_value as $key => $val) {
                if($key === 'Sub-Service'){
                    array_push($sub_service, $val);
                }else{
                    array_push($names, $key);
                    array_push($prices, $val);
                }
            }
            $item->value = [
                'names' => $names,
                'prices' => $prices
            ];
        }
        $data = [
            'dental_optical_primary_care' => $dental_optical_primary_care,
            'sub_service' => $sub_service[0]
        ];
        return response()->json(ResponseFormatter::successResponse($type . ' ' . 'primary care retrieved successfully', $data), 200);
    }

    public function getOtherDentalOpticalServices(Request $request, $type)
    {
        if (!in_array($type, ['dental', 'optical'])){
            return response()->json(ResponseFormatter::errorResponse('type must be dental or optical'), 400);
        }
        if($request->sub_service === null){
            return response()->json(ResponseFormatter::errorResponse('sub_service must be provided'), 400);
        }
        if (!is_bool($request->sub_service)){
            return response()->json(ResponseFormatter::errorResponse('sub service must be true or false of type boolean'), 400);
        }
        if($type === 'dental'){
            if($request->sub_service === true){
                $other_services = DentalOpticalCare::where('type', $type)
                                                                ->where('name', '!=', 'Dental Check Up')
                                                                ->where('name', '!=', 'Occlusal')
                                                                ->get();
            }else{
                $other_services = DentalOpticalCare::where('type', $type)
                                                                ->where('name', '!=', 'Dental Check Up')
                                                                ->get();
            }
        }elseif($type === 'optical'){
            if($request->sub_service === true){
                $other_services = DentalOpticalCare::where('type', $type)
                                                                ->where('name', '!=', 'Eye Check')
                                                                ->where('name', '!=', 'Slit Lamp Examination (gonioscopy/tonometry)')
                                                                ->where('name', '!=', 'Refraction')
                                                                ->where('name', '!=', 'Central Visual Field (CVF)')
                                                                ->where('name', '!=', 'Tonometry')
                                                                ->where('name', '!=', 'Intraoccular Pressure (I.O.P)')
                                                                ->where('name', '!=', 'Pachymetry')
                                                                ->get();
            }else{
                $other_services = DentalOpticalCare::where('type', $type)
                                                                ->where('name', '!=', 'Eye Check')
                                                                ->get();
            }
        }
        foreach ($other_services as $item) {
            $item_value = json_decode($item->value);
            $names = [];
            $prices = [];
            foreach ($item_value as $key => $val) {
                    array_push($names, $key);
                    array_push($prices, $val);
            }
            $item->value = [
                'names' => $names,
                'prices' => $prices
            ];
        }
        $data = [
            'other_services' => $other_services
        ];
        return response()->json(ResponseFormatter::successResponse($type . ' ' . 'services retrieved successfully', $data), 200);
    }

    public function createDentalOpticalCare(DataBaseQueryHandler $dbQueryHandler, HealthServiceRequest $request, $type)
    {
        if (!in_array($type, ['dental', 'optical'])){
            return response()->json(ResponseFormatter::errorResponse('type must be dental or optical'), 400);
        }
        $user = JWTAuth::user();
        $validatedData = $request->validated();
        //check that services fields and values have the same length of array
        $services_input = $validatedData['services'];
        if (count($services_input['fields']) !== count($services_input['values'])) {
            return response()->json(ResponseFormatter::errorResponse('service fields and values array must be of the same length'), 400);
        }
        $services_input_fields = $services_input['fields'];
        $services_input_values = $services_input['values'];
        $final_services = [];
        for ($index = 0; $index < count($services_input_fields); $index++) {
            $value = $services_input_values[$index];
            if($value !== 'No'){
                $service = $dbQueryHandler->getDentalOpticalResult($services_input_fields[$index], $type);
                if(is_array($service)){
                    for($i=0; $i<count($service); $i++){
                        array_push($final_services, $service[$i]);
                    }
                }else{
                    array_push($final_services, $service);
                }
            }
        }
        //create appointment and save it to db
        $appointment = HealthCareAppointment::create([
            'user_id' => $user['id'],
            'service_name' => $request->service_name,
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
        $health_service->service_name = $request->service_name;
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

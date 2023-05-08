<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\ResponseFormatter;
use App\Utils\DataBaseQueryHandler;
use App\Http\Requests\V2\HealthServiceRequest;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use App\Models\Promo;
use App\Models\ComprehensiveCheck;
use App\Models\ComprehensiveCheckCostCentre;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class AppointmentHistoryController extends Controller
{
  public function viewHealthServiceAppointments(Request $request, $type)
  {
    if (!in_array($type, ['past', 'upcoming'])) {
        return response()->json(ResponseFormatter::errorResponse('type must be past or upcoming'), 400);
    }
    $user = JWTAuth::user();
    $operator = $type === 'upcoming' ? '>=' : '<';
    $order_criteria = $type === 'upcoming' ? 'asc' : 'desc';
    $current_date = date('Y-m-d');
    $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
    $appointments = DB::table('users')
        ->join('health_care_appointments', 'users.id', '=', 'health_care_appointments.user_id')
        ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.doctor_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_appointments.comment'])
        ->where(['health_care_appointments.user_id' => $user->id, 'users.role_id' => 1, 'users.is_verified' => true,])
        ->whereDate('health_care_appointments.appointment_date', $operator, $current_date)
        ->orderBy('health_care_appointments.appointment_date', $order_criteria)
        ->paginate($page_items);
    return response()->json(ResponseFormatter::successResponse('health service appointments retrieved successfully', $appointments), 200);
  }

  public function viewLatestAppointment()
  {
    $user = JWTAuth::user();
    $promo = $user->load('promo')->promo;
    $promo_code = (boolean)$promo->is_used === false ? $promo->code : null;
    $promo_discount_percent = (boolean)$promo->is_used === false ? $promo->discount_percent : null;
    $appointment = HealthCareAppointment::where('user_id', $user->id)
                                        ->where('status', 'approved')
                                        ->whereDate('appointment_date', '>=', date('Y-m-d'))
                                        ->orderBy('appointment_date', 'asc')
                                        ->first();
    if($appointment === null){
        return response()->json([
            'status'=>false,
            'message'=>'no latest health service appointment found',
            'data'=> [
                'appointment' => null,
                'promo_code' => $promo_code,
                'promo_discount_percent' => $promo_discount_percent
            ]
        ], Response::HTTP_NOT_FOUND); 
    }
    $services = HealthCareService::where('appointment_id', $appointment->id)->pluck('services');
    $services = json_decode($services[0]);
    $sub_services = [];
    if(is_object($services)) {
        //get sub services for comprehensive checks or screenings
        foreach ($services as $key => $val) {
            for($i=0; $i<count($val); $i++) {
                array_push($sub_services, $val[$i]);
            }
        }
    }
    $data = [
        'services' => $sub_services === [] ? $services : $sub_services,
        'appointment' => $appointment,
        'promo_code' => $promo_code,
        'promo_discount_percent' => $promo_discount_percent,
    ];
    return response()->json(ResponseFormatter::successResponse('latest health service appointment retrieved successfully', $data), 200);
  }
}
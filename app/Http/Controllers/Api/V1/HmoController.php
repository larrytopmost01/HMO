<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Requests\V1\HospitalAppointmentRequest;
use App\Models\HospitalAppointment;
use App\Models\DrugRefill;
use App\Models\SubscriptionHistory;
use App\Models\HealthInsurance;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\Comment;
use App\Models\Subscription;
use App\Utils\ResponseFormatter;
use App\Http\Requests\V1\HealthInsuranceRequest;
use App\Models\DependentRequest;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class HmoController extends Controller
{
    /**
     * books a hospital appointment for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bookHospitalAppointment(HospitalAppointmentRequest $request)
    {
        $user = JWTAuth::user();
        $validatedData = $request->validated();
        $validatedData['user_id'] = $user['id'];

        $doctor_name = $validatedData['doctor_name'] === null ? '' : $validatedData['doctor_name'];
        $comment = $validatedData['comment'] === null ? '' : $validatedData['comment'];

        $validatedData['doctor_name'] = $doctor_name;
        $validatedData['comment'] = $comment;

        $hospitalAppointment = HospitalAppointment::create($validatedData);
        $hospitalAppointment->save();
        if($request->dependent_code !== null){
            $dependent_request = DependentRequest::create([
                'request_id' => $hospitalAppointment->id,
                'request_type' => 'hospital_appointment',
                'principal_code' => $user->enrollee->enrollee_id,
                'dependent_code' => $request->dependent_code
            ]);
            $dependent_request->save();
        }
        return response()->json(ResponseFormatter::successResponse('hospital appointment created successfully', $hospitalAppointment), 201);
    }

    /**
     * books a hospital appointment for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewHospitalAppointments(Request $request, $type)
    {
        if (!in_array($type, ['past', 'upcoming'])) {
            return response()->json(ResponseFormatter::errorResponse('type must be past or upcoming'), 400);
        }
        $user = JWTAuth::user();
        $operator = $type === 'upcoming' ? '>=' : '<';
        $order_criteria = $type === 'upcoming' ? 'asc' : 'desc';
        $current_date = date('Y-m-d');
        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $hospitalAppointments = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('hospital_appointments', 'users.id', '=', 'hospital_appointments.user_id')
            ->select(['hospital_appointments.id', 'hospital_appointments.hospital_name', 'hospital_appointments.doctor_name', 'hospital_appointments.appointment_date', 'hospital_appointments.status', 'hospital_appointments.comment'])
            ->where(['hospital_appointments.user_id' => $user->id, 'enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true,])
            ->whereDate('hospital_appointments.appointment_date', $operator, $current_date)
            ->orderBy('hospital_appointments.appointment_date', $order_criteria)
            ->paginate($page_items);
        return response()->json(ResponseFormatter::successResponse('hospital appointments retrieved successfully', $hospitalAppointments), 200);
    }

    public function rescheduleHospitalAppointment(Request $request, $hospital_appointment_id)
    {
        $validated_data = $request->validate([
            'appointment_date' => 'required|date|unique:hospital_appointments,appointment_date'
        ]);

        $user = JWTAuth::user();
        $rescheduled_appointment_date = $validated_data['appointment_date'];
        $possible_hospital_appointment = HospitalAppointment::where(['id' => $hospital_appointment_id, 'user_id' => $user->id])->first();

        if ($possible_hospital_appointment === null) {
            return response()->json(ResponseFormatter::errorResponse('hospital appointment not found'), 404);
        }

        if ($possible_hospital_appointment->status !== 'pending') {
            $indefinite_article = $possible_hospital_appointment->status === 'approved' ? 'an' : 'a';
            $message = 'You cannot reschedule ' . $indefinite_article . ' ' . $possible_hospital_appointment->status . ' appointment.';
            return response()->json(ResponseFormatter::errorResponse($message), 400);
        }

        $possible_hospital_appointment->appointment_date = $rescheduled_appointment_date;
        $possible_hospital_appointment->save();

        return response()->json(ResponseFormatter::successResponse('hospital appointment rescheduled successfully'), 200);
    }

    public function cancelHospitalAppointment(Request $request, $hospital_appointment_id)
    {
        $user = JWTAuth::user();
        $possible_hospital_appointment = HospitalAppointment::where(['id' => $hospital_appointment_id, 'user_id' => $user->id])->first();

        if ($possible_hospital_appointment === null) {
            return response()->json(ResponseFormatter::errorResponse('hospital appointment not found'), 404);
        }

        if ($possible_hospital_appointment->status !== 'pending') {
            $indefinite_article = $possible_hospital_appointment->status === 'approved' ? 'an' : 'a';
            $message = 'You cannot cancel ' . $indefinite_article . ' ' . $possible_hospital_appointment->status . ' appointment.';
            return response()->json(ResponseFormatter::errorResponse($message), 400);
        }

        $possible_hospital_appointment->delete();

        return response()->json(ResponseFormatter::successResponse('hospital appointment cancelled successfully'), 200);
    }

    /**
     * books a hospital appointment for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestForDrugRefill(Request $request)
    {
        $user = JWTAuth::user();
        $validatedData = $request->validate([
            'reason' => 'required|string',
            'drug_name' => 'required|string'
        ]);
        $validatedData['user_id'] = $user['id'];
        $possible_existing_request = DrugRefill::where('user_id', $user->id)->orderByDesc('created_at')->first();
        if ($possible_existing_request !== null) {
            $current_date = date('Y-m-d');
            $date_from_request = gmdate("Y-m-d", strtotime($possible_existing_request->created_at));
            if ($current_date === $date_from_request) {
                return response()->json(ResponseFormatter::errorResponse('you have already requested for drug refill today'), 409);
            }
        }
        $newRequest = DrugRefill::create($validatedData);
        if($request->dependent_code !== null){
            $dependent_request = DependentRequest::create([
                'request_id' => $newRequest->id,
                'request_type' => 'drug_refill',
                'principal_code' => $user->enrollee->enrollee_id,
                'dependent_code' => $request->dependent_code
            ]);
            $dependent_request->save();
        }
        return response()->json(ResponseFormatter::successResponse('drug refill request created successfully', $newRequest), 201);
    }

    public function teleMedicine()
    {
        return response()->json(ResponseFormatter::successResponse('you are an active enrollee', null), 200);
    }

    public function comment(Request $request){
        $user = JWTAuth::user();
        $comments= new Comment;
        $comments->user_id = $request->$user_id;
        $comments->comment = $request->$comment;
        $comments->save();
        return response()->json(ResponseFormatter::successResponse('Comment successfully'), 200);

    }
}

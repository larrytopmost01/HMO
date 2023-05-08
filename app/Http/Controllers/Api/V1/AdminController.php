<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\EnrolleeRequestCard;
use App\Models\EnrolleeRequestCode;
use App\Models\DrugRefill;
use App\Models\Comment;
use App\Models\Enrollee;
use App\Models\User;
use App\Utils\Proxy;
use App\Models\Subscription;
use App\Models\SubscriptionHistory;
use App\Models\HealthInsurance;
use App\Models\HospitalAppointment;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Utils\Message;
use App\Utils\Jobs;
use App\Utils\ResourceTransformer;
use App\Utils\ObjectTransformer;
use App\Utils\HttpResponse;

class AdminController extends Controller
{
    public function approveOrDeclineDrugRefill($drug_refill_id, $status)
    {
        if (!in_array($status, ['approved', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be approved or declined'), 400);
        }

        $possible_drug_refill = DrugRefill::where('id', $drug_refill_id)->first();
        if ($possible_drug_refill === null) {
            return response()->json(ResponseFormatter::errorResponse('drug refill request not found'), 404);
        }

        $possible_drug_refill->status = $status;
        $possible_drug_refill->save();
        $user = User::find($possible_drug_refill->user_id);
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Drug Refill Request',
            'message' => 'The drug refill you requested for, on the ' . date('jS F Y', strtotime($possible_drug_refill->created_at)) . ' has been ' . $status,
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);

        return response()->json(ResponseFormatter::successResponse('drug refill ' . $status . ' successfully', null), 200);
    }

    public function getAllEnrollees(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $subscription_status = $request->subscription_status === null ? 'all' : $request->subscription_status;
        $enrollees = null;
        if ($subscription_status === 'all') {
            $enrollees = DB::table('users')
                ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
                ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
                ->select(['enrollees.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.created_at', 'enrollees.enrollee_id', 'subscriptions.plan_name', 'subscriptions.status'])
                ->where(['enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true])
                ->where(function ($query) {
                    $query->where('subscriptions.status', '=', 'active')
                        ->orWhere('subscriptions.status', '=', 'inactive');
                })
                ->orderBy('subscriptions.updated_at', 'desc')
                ->paginate($page_items);
        } else {
            $enrollees = DB::table('users')
                ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
                ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
                ->select(['enrollees.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.created_at', 'subscriptions.plan_name', 'subscriptions.status'])
                ->where(['users.is_verified' => true, 'users.role_id' => 1, 'subscriptions.status' => 'pending'])
                ->orderBy('subscriptions.updated_at', 'desc')
                ->paginate($page_items);
        }
        return response()->json(ResponseFormatter::successResponse((new Message('MSG_11'))->message, $enrollees), 200);
    }

    /**
     * @param Enrollee id
     * @return \Illuminate\Http\Response
     */
    public function getEnrolleeById($id)
    {
        $data = [];
        $enrollee = Enrollee::find($id);
        if ($enrollee === null) {
            return response()->json(ResponseFormatter::errorResponse('enrollee not found'), 404);
        }
        $user = User::where('id', $enrollee->user_id)->first();
        $status =  isset($user->subscription) ? $user->subscription->status : null;
        $data['enrollee_details']['name'] = $user->first_name . ' ' . $user->last_name;
        $data['enrollee_details']['email'] = $user->email;
        $data['enrollee_details']['phone_number'] = $user->phone_number;
        $data['enrollee_details']['date_created'] = $enrollee->created_at;
        $data['enrollee_details']['plan_name'] = $enrollee->plan;
        $data['enrollee_details']['status'] = $status;
        $data['enrollee_details']['hospital'] = $enrollee->hospital_name;
        $data['enrollee_details']['enrollee_id'] = $enrollee->enrollee_id;

        //get plan benefits
        $current_plan = $enrollee->plan;
        $user_id = $enrollee->user_id;
        $final_enrollee_benefits = null;

        if ($current_plan === 'custom') {
            $possible_health_insurance = HealthInsurance::where('user_id', $user_id)->orderByDesc('created_at')->first();
            if ($possible_health_insurance === null) {
                return response()->json(ResponseFormatter::errorResponse('health insurance not found'), 404);
            }

            $decoded_benefits = json_decode($possible_health_insurance->benefits);
            $plan_benefits = [];
            foreach ($decoded_benefits as $key => $val) {
                $benefit_string = $key . ' - ' . $val;
                array_push($plan_benefits, $benefit_string);
            }

            $final_enrollee_benefits = $plan_benefits;
        } else {
            $proxy = new Proxy();
            $client_code = substr($enrollee->enrollee_id, 0, 3);
            $response = $proxy->getPlanBenefits(strtolower($enrollee->plan), strtolower($client_code));
            if ($response->getStatusCode() == Response::HTTP_OK) {
                $benefits = (array) json_decode($response->getBody()->getContents(), true);
                $final_enrollee_benefits = $benefits['plan_benefits'];
            } else {
                $benefits = null;
            }
        }

        // $data['enrollee_details']['benefits'] = isset($benefits['plan_benefits']) ? $benefits['plan_benefits'] : null;
        $data['enrollee_details']['benefits'] = $final_enrollee_benefits;

        //get card requests history for user
        $card_requests = EnrolleeRequestCard::where('user_id', $user->id)->get();
        $data['card_requests_history'] = $card_requests;

        //get authorization code request history for user
        $auth_code_requests = EnrolleeRequestCode::where('user_id', $user->id)->get();
        $data['auth_code_requests_history'] = $auth_code_requests;

        //get drug refill request history for user
        $drug_refill_requests = DrugRefill::where('user_id', $user->id)->get();
        $data['drug_refill_requests_history'] = $drug_refill_requests;

        //get appointment request history for user
        $appointment_requests = HospitalAppointment::where('user_id', $user->id)->get();
        $data['appointment_requests_history'] = $appointment_requests;

        return response()->json(ResponseFormatter::successResponse('Enrollee records retrieved successfully', $data), 200);
    }

    public function getNonEnrollees(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $non_enrollees = DB::table('users')
            ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->select(['users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.created_at'])
            ->where(['users.is_verified' => true, 'users.role_id' => 1, 'subscriptions.status' => null])
            ->orderBy('users.created_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('non-enrollees retrieved successfully', $non_enrollees), 200);
    }

    public function acceptPendingEnrollee(Request $request, $enrollee_primary_key)
    {
        $validated_data = $request->validate([
            'enrollee_id' => 'required|string'
        ]);

        $enrollee_id = $validated_data['enrollee_id'];
        $possible_enrollee = Enrollee::where('id', $enrollee_primary_key)->first();

        if ($possible_enrollee === null) {
            return response()->json(ResponseFormatter::errorResponse('enrollee not found'), 404);
        }

        $possible_enrollee->enrollee_id = $enrollee_id;
        $possible_enrollee->save();

        // update subscription
        $user = User::where('id', $possible_enrollee->user_id)->first();
        $user_id = $user->id;
        $possible_subscription = Subscription::where('user_id', $user_id)->first();
        if ($possible_subscription !== null) {
            // calculate end date
            $current_unix_time = strtotime(date('Y-m-d'));
            $new_end_date = date('Y-m-d', (86400 * 364) + $current_unix_time);

            $possible_subscription->status = 'active';
            $possible_subscription->plan_name = 'custom';
            $possible_subscription->start_date = date('Y-m-d');
            $possible_subscription->end_date = $new_end_date;

            $possible_subscription->save();
        }

        // update subscription history
        $possible_health_insurance = HealthInsurance::where('user_id', $user_id)->orderByDesc('created_at')->first();
        if ($possible_health_insurance === null) {
            return response()->json(ResponseFormatter::errorResponse('health insurance not found'), 404);
        }

        $possible_subscription_history = SubscriptionHistory::where('health_insurance_id', $possible_health_insurance->id)->first();
        if ($possible_subscription_history === null) {
            return response()->json(ResponseFormatter::errorResponse('subscription history not found'), 404);
        }

        $possible_subscription_history->start_date = date('Y-m-d');
        $possible_subscription_history->end_date = $new_end_date;
        $possible_subscription_history->save();

        // send email
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Health Insurance Subscription',
            'message' => 'Your health insurance subscription has been approved. Your Enrollee ID is: ' . $enrollee_id
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);

        return response()->json(ResponseFormatter::successResponse('enrollee accepted successfully', null), 200);
    }

    public function getCardRequests(Request $request, $status)
    {
        if (!in_array($status, ['approved', 'pending', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved, pending or declined'), 400);
        }

        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $enrollees = null;
        $enrollees = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('enrollee_request_cards', 'users.id', '=', 'enrollee_request_cards.user_id')
            ->select(['enrollees.id AS enrollee_primary_key', 'users.first_name', 'users.last_name', 'enrollees.enrollee_id', 'enrollee_request_cards.id', 'enrollee_request_cards.card_collected', 'enrollee_request_cards.status', 'enrollee_request_cards.created_at', 'enrollee_request_cards.passport_url'])
            ->where(['subscriptions.status' => 'active', 'enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'enrollee_request_cards.status' => $status])
            ->orderBy('enrollee_request_cards.updated_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('card requests retrieved successfully', $enrollees), 200);
    }

    public function updatePendingCardRequest($id, $status)
    {
        if (!in_array($status, ['approved', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be approved or declined'), 400);
        }

        $enrolleeRequestCard = EnrolleeRequestCard::find($id);

        if ($enrolleeRequestCard === null) {
            return response()->json(ResponseFormatter::errorResponse('card request not found'), 404);
        }

        $enrolleeRequestCard->status = $status;
        $enrolleeRequestCard->update();
        $user = User::find($enrolleeRequestCard->user_id);
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'HMO Card Request',
            'message' => 'The hmo card you requested for, on the ' . date('jS F Y', strtotime($enrolleeRequestCard->created_at)) . ' has been ' . $status,
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);

        return response()->json(ResponseFormatter::successResponse('card request ' . $status . ' successfully', null), 200);
    }

    public function viewDrugRefills(Request $request, $status)
    {
        if (!in_array($status, ['approved', 'pending', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved, pending or declined'), 400);
        }

        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $enrollees = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('drug_refills', 'users.id', '=', 'drug_refills.user_id')
            ->select(['enrollees.id AS enrollee_primary_key', 'users.first_name', 'users.last_name', 'users.phone_number', 'enrollees.enrollee_id', 'drug_refills.id', 'drug_refills.drug_name', 'drug_refills.reason', 'drug_refills.created_at'])
            ->where(['subscriptions.status' => 'active', 'enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'drug_refills.status' => $status])
            ->orderBy('drug_refills.updated_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('drug refill requests retrieved successfully', $enrollees), 200);
    }

    public function approveOrDeclineHospitalAppointment($hospital_appointment_id, $status)
    {
        if (!in_array($status, ['approved', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be approved or declined'), 400);
        }

        $possible_hospital_appointment =  HospitalAppointment::where('id', $hospital_appointment_id)->first();
        if ($possible_hospital_appointment === null) {
            return response()->json(ResponseFormatter::errorResponse('hospital appointment not found'), 404);
        }

        $possible_hospital_appointment->status = $status;
        $possible_hospital_appointment->save();
        $user = User::find($possible_hospital_appointment->user_id);
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Hospital Appointment',
            'message' => 'The appointment you booked with ' . $possible_hospital_appointment->hospital_name . ' on the ' . date('jS \of F Y h:i A', strtotime($possible_hospital_appointment->created_at)) . ' has been ' . $status,
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);

        return response()->json(ResponseFormatter::successResponse('hospital appointment ' . $status . ' successfully'), 200);
    }

    public function viewHospitalAppointments(Request $request, $status)
    {
        if (!in_array($status, ['approved', 'pending', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved, pending or declined'), 400);
        }

        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $enrollees = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('hospital_appointments', 'users.id', '=', 'hospital_appointments.user_id')
            ->select(['enrollees.id AS enrollee_primary_key', 'users.first_name', 'users.last_name', 'users.phone_number', 'enrollees.enrollee_id', 'hospital_appointments.id', 'hospital_appointments.hospital_name', 'hospital_appointments.doctor_name', 'hospital_appointments.appointment_date', 'hospital_appointments.comment', 'hospital_appointments.created_at'])
            ->where(['subscriptions.status' => 'active', 'enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'hospital_appointments.status' => $status])
            ->orderBy('hospital_appointments.updated_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('hospital appointments retrieved succesfully', $enrollees));
    }
    public function dashboard(ObjectTransformer $objectTransformer)
    {
        $data = [];
        //total enrollee
        $data['total_enrollees'] = Enrollee::count();
        //total requests
        $data['total_requests'] = EnrolleeRequestCard::count() + DrugRefill::count() + HospitalAppointment::count() + EnrolleeRequestCode::count();
        //total appointments
        $data['total_appointments'] = HospitalAppointment::count();
        //plan type
        $data['plan_type']['guard'] = Subscription::where('plan_name', 'guard')->count();
        $data['plan_type']['shield'] = Subscription::where('plan_name', 'shield')->count();
        $data['plan_type']['exclusives'] = Subscription::where('plan_name', 'exclusives')->count();
        $data['plan_type']['premium'] = Subscription::where('plan_name', 'premium')->count();
        $data['plan_type']['special'] = Subscription::where('plan_name', 'special')->count();
        $data['plan_type']['custom'] = Subscription::where('plan_name', 'custom')->count();
        $data['requests'] = [];
        $custom_requests = [];
        //request type: card requests
        $card_requests = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('enrollee_request_cards', 'users.id', '=', 'enrollee_request_cards.user_id')
            ->select(['enrollees.name', 'enrollee_request_cards.created_at', 'enrollee_request_cards.id'])
            ->where(['enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'enrollee_request_cards.status' => 'pending', 'subscriptions.status' => 'active'])
            ->orderBy('enrollee_request_cards.updated_at', 'desc')
            ->limit(1)
            ->get();
        array_push($custom_requests, $objectTransformer->getTransformedObject($card_requests, 'Card Request'));
        //request type: drug refill
        $drug_refill = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('drug_refills', 'users.id', '=', 'drug_refills.user_id')
            ->select(['enrollees.name', 'drug_refills.created_at', 'drug_refills.id'])
            ->where(['enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'drug_refills.status' => 'pending', 'subscriptions.status' => 'active'])
            ->orderBy('drug_refills.updated_at', 'desc')
            ->limit(1)
            ->get();
        array_push($custom_requests, $objectTransformer->getTransformedObject($drug_refill, 'Drug Refill'));
        //request type: appointment
        $appointment = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('hospital_appointments', 'users.id', '=', 'hospital_appointments.user_id')
            ->select(['enrollees.name', 'hospital_appointments.created_at', 'hospital_appointments.id'])
            ->where(['enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'hospital_appointments.status' => 'pending', 'subscriptions.status' => 'active'])
            ->orderBy('hospital_appointments.updated_at', 'desc')
            ->limit(1)
            ->get();
        array_push($custom_requests, $objectTransformer->getTransformedObject($appointment, 'Appointment'));
        //request type: authorization code
        $auth_code = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('enrollee_request_codes', 'users.id', '=', 'enrollee_request_codes.user_id')
            ->select(['enrollees.name', 'enrollee_request_codes.created_at', 'enrollee_request_codes.id'])
            ->where(['enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'enrollee_request_codes.status' => 'pending', 'subscriptions.status' => 'active'])
            ->orderBy('enrollee_request_codes.updated_at', 'desc')
            ->limit(1)
            ->get();
        array_push($custom_requests, $objectTransformer->getTransformedObject($auth_code, 'Auth Code'));
        $data['requests'] = $custom_requests;

        //card requests occurence: first time total
        $data['card_requests_occurrence']['first_time']['total'] = EnrolleeRequestCard::select(DB::raw('count(*) as total, created_at'))
            ->where('card_collected', false)->count();

        //card requests occurence: repested request total
        $data['card_requests_occurrence']['repeated_request']['total'] = EnrolleeRequestCard::select(DB::raw('count(*) as total, created_at'))
            ->where('card_collected', true)->count();

        //card requests occurence: first time time-stamps
        $data['card_requests_occurrence']['first_time']['time_stamps'] = EnrolleeRequestCard::distinct()
            ->where('card_collected', false)
            ->orderBy('created_at')
            ->pluck('created_at');

        //card requests occurence: repeated request time-stamps
        $data['card_requests_occurrence']['repeated_request']['time_stamps'] =  EnrolleeRequestCard::distinct()
            ->where('card_collected', true)
            ->orderBy('created_at')
            ->pluck('created_at');
        //enrollee status: active enrollees total
        $data['enrollee_status']['active_enrollees']['total'] = Subscription::select(DB::raw('count(*) as total, created_at'))
            ->where('status', 'active')->count();

        //enrollee status: pending enrollees total
        $data['enrollee_status']['pending_enrollees']['total'] = Subscription::select(DB::raw('count(*) as total, created_at'))
            ->where('status', 'pending')->count();
        //enrollee status: non enrollees total
        $data['enrollee_status']['non_enrollees']['total'] = DB::table('users')
            ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->where(['users.is_verified' => true, 'users.role_id' => 1, 'subscriptions.status' => null])
            ->count();

        //enrollee status: declined enrollees total
        $data['enrollee_status']['declined_enrollees']['total'] = Subscription::select(DB::raw('count(*) as total, created_at'))
            ->where('status', 'declined')->count();

        //enrollee status: active enrollees time-stamps
        $data['enrollee_status']['active_enrollees']['time_stamps'] = Subscription::distinct()
            ->where('status', 'active')
            ->orderBy('created_at')
            ->pluck('created_at');

        //enrollee status: pending enrollees time-stamps
        $data['enrollee_status']['pending_enrollees']['time_stamps'] = Subscription::distinct()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->pluck('created_at');

        //enrollee status: declined enrollees time-stamps
        $data['enrollee_status']['declined_enrollees']['time_stamps'] = Subscription::distinct()
            ->where('status', 'declined')
            ->orderBy('created_at')
            ->pluck('created_at');

        //enrollee status: non enrollees time-stamps
        $data['enrollee_status']['non_enrollees']['time_stamps'] = DB::table('users')
            ->leftJoin('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->pluck('users.created_at')
            ->where(['users.is_verified' => true, 'users.role_id' => 1, 'subscriptions.status' => null]);

        return response()->json(ResponseFormatter::successResponse('data retrieved successfully', $data), 200);
    }

    public function getCardRequestById($id)
    {
        $data = EnrolleeRequestCard::find($id);
        if ($data !== null) {
            return response()->json(ResponseFormatter::successResponse('caard request details retrieved successfully', $data), 200);
        }
        return response()->json(ResponseFormatter::errorResponse('card request not found'), 404);
    }

    public function getDrugRefillById($id)
    {
        $data = DrugRefill::find($id);
        if ($data !== null) {
            return response()->json(ResponseFormatter::successResponse('drug refill details retrieved successfully', $data), 200);
        }
        return response()->json(ResponseFormatter::errorResponse('drug refill not found'), 404);
    }

    public function getHospitalAppointmentById($id)
    {
        $data = HospitalAppointment::find($id);
        if ($data !== null) {
            return response()->json(ResponseFormatter::successResponse('hospital appointment details retrieved successfully', $data), 200);
        }
        return response()->json(ResponseFormatter::errorResponse('hospital appointment not found'), 404);
    }

    public function getCodeRequests(Request $request, $status)
    {
        if (!in_array($status, ['approved', 'pending', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved, pending or declined'), 400);
        }

        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $enrollees = null;
        $enrollees = DB::table('users')
            ->join('enrollees', 'users.id', '=', 'enrollees.user_id')
            ->join('subscriptions', 'users.id', '=', 'subscriptions.user_id')
            ->join('enrollee_request_codes', 'users.id', '=', 'enrollee_request_codes.user_id')
            ->select(['enrollees.id AS enrollee_primary_key', 'users.first_name', 'users.last_name', 'enrollees.enrollee_id', 'enrollee_request_codes.id', 'enrollee_request_codes.hospital_name', 'enrollee_request_codes.status', 'enrollee_request_codes.created_at', 'enrollee_request_codes.request_message', 'enrollee_request_codes.request_code'])
            ->where(['subscriptions.status' => 'active', 'enrollees.is_verified' => true, 'users.role_id' => 1, 'users.is_verified' => true, 'enrollee_request_codes.status' => $status])
            ->orderBy('enrollee_request_codes.updated_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('code requests retrieved successfully', $enrollees), 200);
    }

    public function updatePendingCodeRequest(Request $request, $id, $status)
    {
        if (!in_array($status, ['approved', 'declined'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be approved or declined'), 400);
        }

        if ($status === 'approved' && $request->request_code == null) {
            return response()->json(ResponseFormatter::errorResponse('request code field is required'), 400);
        }

        $enrolleeRequestCode = EnrolleeRequestCode::find($id);
        if ($enrolleeRequestCode === null) {
            return response()->json(ResponseFormatter::errorResponse('auth code request not found'), 404);
        }

        if ($status === 'approved') {
            $enrolleeRequestCode->request_code = $request->request_code;
        }

        $enrolleeRequestCode->status = $status;
        $enrolleeRequestCode->update();
        $user = User::find($enrolleeRequestCode->user_id);
        $to = $user->email;
        $message_suffix = $status === 'approved' ? '. Your authorization code is ' . $request->request_code : '.';
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Auth Code Request',
            'message' => 'The authorization code you requested for, on the ' . date('jS F Y', strtotime($enrolleeRequestCode->created_at)) . ' has been ' . $status . $message_suffix
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);

        return response()->json(ResponseFormatter::successResponse('code request ' . $status . ' successfully', null), 200);
    }

    public function getRequestCodeById($id)
    {
        $data = EnrolleeRequestCode::find($id);
        if ($data !== null) {
            return response()->json(ResponseFormatter::successResponse('auth code request details retrieved successfully', $data), 200);
        }
        return response()->json(ResponseFormatter::errorResponse('auth code request not found'), 404);
    }


    public function getNonEnrolleeById($id)
    {
        $data = User::find($id);
        if ($data !== null) {
            return response()->json(ResponseFormatter::successResponse('user details retrieved successfully', $data), 200);
        }
        return response()->json(ResponseFormatter::errorResponse('user not found'), 404);
    }

    public function view_comment(){

        $comment = Comments::all();
        return response()->json(ResponseFormatter::successResponse( $comment), 200);


    }
}
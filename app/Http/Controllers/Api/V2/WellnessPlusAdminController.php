<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\V2\Controller as Controller;
use App\Models\User;
use App\Models\Hospital;
use App\Models\HospitalLevel;
use App\Utils\ResponseFormatter;
use App\Models\HealthCareAppointment;
use App\Models\HealthCareService;
use App\Models\Service;
use App\Models\HealthServiceProviders;
use App\Utils\Jobs;

class WellnessPlusAdminController extends Controller
{
    // Get all users
    public function getUsers(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $data = User::select(['users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.created_at'])
            ->where('users.role_id', '=', 1)
            ->orderBy('users.created_at', 'desc')
            ->paginate($page_items);

        return response()->json(ResponseFormatter::successResponse('All Users retrieved successfully', $data), 200);
    }

    // Get single user appointments history
    public function getUserById($id)
    {
        $data = [];
        $user = User::find($id);
        if ($user === null) {
            return response()->json(ResponseFormatter::errorResponse('User not found'), 404);
        }
        $user_data = User::select(['users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone_number', 'users.created_at'])
            ->where('users.id', '=', $id)
            ->first();
        
        $data['user_data'] = $user_data;       
        
        $data['dental_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', '=', 'dental')
            ->get();
        $data['optical_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', '=', 'optical')
            ->get();
        $data['comprehensive_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', 'LIKE', '%' . 'comprehensive' . '%')
            ->get();
        $data['pre_employment_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', 'LIKE', '%' . 'pre-employment' . '%')
            ->get();
        $data['basic_health_check_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', '=', 'basic-health-check')
            ->get();
        $data['cancer_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select(['health_care_appointments.id', 'health_care_appointments.hospital_name', 'health_care_appointments.appointment_date', 'health_care_appointments.status', 'health_care_services.amount_paid'])
            ->where('health_care_appointments.user_id', '=', $id)
            ->where('health_care_appointments.service_name', 'LIKE', '%' . 'cancer' . '%')
            ->get();

        return response()->json(ResponseFormatter::successResponse('User details retrieved successfully', $data), 200);
    }

    // Get filtered health care appointments
    public function getAppointments(Request $request, $type, $status)
    {
        if (!in_array($status, ['approved', 'pending'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved or pending'), 400);
        }
        if (!in_array($type, ['dental', 'optical', 'comprehensive', 'pre-employment', 'cancer', 'basic-health-check'])) {
            return response()->json(ResponseFormatter::errorResponse('type must be: dental, optical, comprehensive, pre-employment, cancer or basic-health-check'), 400);
        }
        
        $page_items = $request->page_items !== null ? (int) $request->page_items : 10;
        $data = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select('users.id as user_id', 'users.first_name as first_name', 'users.last_name as last_name', 'health_care_appointments.service_name as service_name', 'health_care_appointments.doctor_name as doctor_name', 'health_care_appointments.hospital_name as hospital_name', 'health_care_appointments.appointment_date as appointment_date', 'health_care_appointments.status as status', 'health_care_services.amount_paid as amount_paid')
            ->where('health_care_appointments.status', $status)
            ->where('health_care_appointments.service_name', 'LIKE', '%' . $type . '%')
            ->orderBy('health_care_appointments.created_at')
            ->paginate($page_items);        
        return response()->json(ResponseFormatter::successResponse('Health care appointments retrieved successfully', $data), 200);
    }
    
    /** 
     * @param Appointment id
     * @return \Illuminate\Http\Response
     */
    // Get appointment details
    public function getAppointmentDetails($id)
    {
        $data = [];
        $appointment = HealthCareAppointment::find($id);
        if ($appointment === null) {
            return response()->json(ResponseFormatter::errorResponse('Appointment not found'), 404);
        }
        $service = HealthCareService::select('services', 'service_name', 'amount_paid', 'created_at', 'appointment_id')->where('appointment_id', $appointment->id)->first();
        //json decode services
        $services = json_decode($service->services);
        $sub_services = [];
        if(is_object($services)) {
            //get sub services for comprehensive checks or screening
            foreach ($services as $key => $val) {
                for($i=0; $i<count($val); $i++) {
                    array_push($sub_services, $val[$i]);
                }
            }
        }
        $data = [
            'appointment' => $appointment,
            'sub_services' => $sub_services === [] ? $services : $sub_services,
            'amount_paid' => $service->amount_paid
        ];
        return response()->json(ResponseFormatter::successResponse('Appointment details retrieved successfully', $data), 200);
    }   

    /**
     * @param Appointment appointment_id
     * @param String status
     * @return \Illuminate\Http\Response
     */
    // Update appointment status
    public function updateAppointmentStatus(Request $request, $appointment_id, $status)
    {
        // Validate that status change was requested
        if(!in_array($status, ['approved', 'pending'])) {
            return response()->json(ResponseFormatter::errorResponse('status must be: approved or pending'), 400);
        }
        // Should retrieve valid appointment id
        $appointment = HealthCareAppointment::find($appointment_id);
        if ($appointment === null) {
            return response()->json(ResponseFormatter::errorResponse('Appointment not found'), 404);
        }
        if ($status === 'approved' && $request->request_code == null) {
            return response()->json(ResponseFormatter::errorResponse('request code field is required'), 400);
        }
        // Update appointment status
        $appointment->status = $status;
        $appointment->update();

        // Send mail to user informing about appointment status
        $user = User::find($appointment->user_id);
        $to=$user->email;
        $subject="Wellness appointment status update";
        $message_suffix = $status === 'approved' ? '. Your authorization code is ' . $request->request_code : '.';

        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => $subject,
            'message' => 'The appointment request you made on the ' . date('jS F Y', strtotime($appointment->appointment_date)) . ' at ' . date('h:i A', strtotime($appointment->created_at)) . ' has been ' . $appointment->status . ' ' . $message_suffix . '.',
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);
        return response()->json(ResponseFormatter::successResponse('Appointment status updated successfully'), 200);
    }

    public function getHospitalById($id)
    {
        $hospital = Hospital::find($id);
        if ($hospital === null) {
            return response()->json(ResponseFormatter::errorResponse('Hospital not found'), 404);
        }
        return response()->json(ResponseFormatter::successResponse('Hospital retrieved successfully', $hospital), 200);
    }

    // Dashboard function
    public function dashboard()
    {
        $data = [];
        // Total users
        $data['total_users'] = User::count();
        // Total appointments
        $data['total_appointments'] = HealthCareAppointment::count();
        // Total pending appointments
        $data['total_pending_appointments'] = HealthCareAppointment::where('status', 'pending')->count();
        // Last five appointments and amount paid
        $data['recent_pending_appointments'] = DB::table('health_care_appointments')
            ->join('users', 'health_care_appointments.user_id', '=', 'users.id')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select('users.id as user_id', 'users.first_name as first_name', 'users.last_name as last_name', 'health_care_appointments.id as appointment_id', 'health_care_appointments.service_name as service_name',  'health_care_appointments.appointment_date as appointment_date', 'health_care_services.amount_paid as amount_paid')
            ->where('health_care_appointments.status', 'pending')
            ->orderBy('health_care_appointments.created_at', 'desc')
            ->limit(5)
            ->get();    

        $data['top_appointments'] = DB::table('health_care_appointments')
            ->select('service_name', DB::raw('count(*) as top_appointments'))
            ->groupBy('service_name')
            ->orderBy('top_appointments', 'desc')
            ->get();
        $data['highest_paid_appointments'] = DB::table('health_care_appointments')
            ->join('health_care_services', 'health_care_appointments.id', '=', 'health_care_services.appointment_id')
            ->select('health_care_appointments.service_name as service_name', DB::raw('sum(health_care_services.amount_paid) as total_amount_paid'))
            ->groupBy('health_care_appointments.service_name')
            ->orderBy('total_amount_paid', 'desc')
            ->get();

        return response()->json(ResponseFormatter::successResponse('Dashboard data retrieved successfully', $data), 200);
    }

    // Hospitals Module Functions
    public function getHospitals(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 50;
        $data = Hospital::orderBy('hospitals.name')->paginate($page_items); 
        return response()->json(ResponseFormatter::successResponse('Hospitals retrieved successfully', $data), 200);
    }
    // Hospitals Module Functions
    public function getHospitalsForAdmin(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 20;        
        $data = DB::table('hospitals')
            ->select(['hospitals.id as id', 'hospitals.name as name', 'hospitals.address as address', 'hospitals.location as location', 'hospitals.plan as plan', 'hospitals.level as level', 'hospitals.created_at as created_at', 'hospitals.updated_at as updated_at'])
            ->orderBy('hospitals.id', 'desc')
            ->paginate($page_items);
        return response()->json(ResponseFormatter::successResponse('Hospitals retrieved successfully', $data), 200);
    }
    // Get Hospital levels
    public function getHospitalLevels()
    {
        $data = HospitalLevel::orderBy('hospital_levels.id')->get(); 
        return response()->json(ResponseFormatter::successResponse('Hospital levels retrieved successfully', $data), 200);
    }
    /**
     * Store a newly created hospital in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeHospital(Request $request) {
        $input = $request->all();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:100',
            'plan' => 'required|string|max:20',
            'level' => 'required|integer|numeric',
        ]);
        $hospital = Hospital::create($validated);
        return response()->json(ResponseFormatter::successResponse('Hospital created successfully', $hospital), 200);
    }
    /**
     * Display a specified hospital.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showHospitalById($id) {
        $hospital = Hospital::find($id);
        if ($hospital === null) {
            return response()->json(ResponseFormatter::errorResponse('Hospital not found'), 404);
        }
        return response()->json(ResponseFormatter::successResponse('Hospital retrieved successfully', $hospital), 200);
    }
    /**
     * Update the specified hospital in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateHospitalById(Request $request, $id) {
        $input = $request->all();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:100',
            'plan' => 'required|string|max:20',
            'level' => 'required|integer|numeric',
        ]);
        $hospital = Hospital::find($id);
        if ($hospital === null) {
            return response()->json(ResponseFormatter::errorResponse('Hospital not found'), 404);
        }
        $hospital->update($validated);
        return response()->json(ResponseFormatter::successResponse('Hospital updated successfully', $hospital), 200);
    }
    /**
     * Remove the specified hospital from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteHospitalById($id) {
        $hospital = Hospital::find($id);
        if ($hospital === null) {
            return response()->json(ResponseFormatter::errorResponse('Hospital not found'), 404);
        }
        $hospital->delete();
        return response()->json(ResponseFormatter::successResponse('Hospital deleted successfully'), 200);
    }
    // function to get services
    public function getServices(Request $request)
    {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 50;
        $data = Service::orderBy('services.id')->paginate($page_items); 
        return response()->json(ResponseFormatter::successResponse('Services retrieved successfully', $data), 200);
    }
    /**
     * Store a newly created service in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeService(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $service = Service::create($validated);
        return response()->json(ResponseFormatter::successResponse('Service created successfully', $service), 200);
    }
    /**
     * Display a specified service
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showServiceById($id){
        $service = Service::find($id);
        if ($service === null) {
            return response()->json(ResponseFormatter::errorResponse('Service not found'), 404);
        }
        return response()->json(ResponseFormatter::successResponse('Service retrieved successfully', $service), 200);
    }
    /**
     * Update the specified service in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateServiceById(Request $request, $id){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $service = Service::find($id);
        if ($service === null) {
            return response()->json(ResponseFormatter::errorResponse('Service not found'), 404);
        }
        $service->update($validated);
        return response()->json(ResponseFormatter::successResponse('Service updated successfully', $service), 200);
    }
    /**
     * Remove the specified service from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteServiceById($id) {
        $service = Service::find($id);
        if ($service === null) {
            return response()->json(ResponseFormatter::errorResponse('Service not found'), 404);
        }
        $service->delete();
        return response()->json(ResponseFormatter::successResponse('Service deleted successfully'), 200);
    }
    // function to get health service providers
    public function getHealthServiceProviders(Request $request) {
        $page_items = $request->page_items !== null ? (int) $request->page_items : 50;
        $data = DB::table('health_service_providers')
            ->join('services', 'health_service_providers.service_id', '=', 'services.id')
            ->select('health_service_providers.*', 'services.name as service_name')
            ->orderBy('health_service_providers.id', 'desc')
            ->paginate($page_items);
        return response()->json(ResponseFormatter::successResponse('Health service providers retrieved successfully', $data), 200);
    }
    /**
     * Store a newly created health service provider in storage.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeHealthServiceProvider(Request $request) {
        $validated = $request->validate([
            'location' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',            
            'service_id' => 'required|integer|numeric',
        ]);
        $health_service_provider = HealthServiceProviders::create($validated);
        return response()->json(ResponseFormatter::successResponse('Health service provider created successfully', $health_service_provider), 200);
    }
    /**
     * Display a specified health service provider.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function showHealthServiceProviderById($id) {
        $health_service_provider = HealthServiceProviders::find($id);
        if ($health_service_provider === null) {
            return response()->json(ResponseFormatter::errorResponse('Health service provider not found'), 404);
        }
        return response()->json(ResponseFormatter::successResponse('Health service provider retrieved successfully', $health_service_provider), 200);
    }
    /**
     * Update the specified health service provider in storage.
     * @param \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function updateHealthServiceProviderById(Request $request, $id){
        $input = $request->all();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'location' => 'required|string|max:100',
            'service_id' => 'required|integer|numeric',
        ]);
        $health_service_provider = HealthServiceProviders::find($id);
        if ($health_service_provider === null) {
            return response()->json(ResponseFormatter::errorResponse('Health service provider not found'), 404);
        }
        $health_service_provider->update($validated);
        return response()->json(ResponseFormatter::successResponse('Health service provider updated successfully', $health_service_provider), 200);
    }
    /**
     * Remove the specified health service provider from storage.
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteServiceProviderById($id) {
        $health_service_provider = HealthServiceProviders::find($id);
        if ($health_service_provider === null) {
            return response()->json(ResponseFormatter::errorResponse('Health service provider not found'), 404);
        }
        $health_service_provider->delete();
        return response()->json(ResponseFormatter::successResponse('Health service provider deleted successfully'), 200);
    }
 }
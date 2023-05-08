<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Comment;
use App\Models\Enrollee;
use App\Models\Subscription;
use App\Models\DrugRefill;
use App\Models\HospitalAppointment;
use App\Models\DependentRequest;
use DateTime;
use JWTAuth;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class HmoManagementTest extends TestCase
{
    // private $code = "GDM/22/1000/A";
    private $code = "GDM/22/1000/A";
    use RefreshDatabase;

    /**
     *@param void
     *@return token
     */
    public function authenticateUser()
    {
        $this->createUser();
        $user = User::where('email', 'john@example.com')->first();
        $token = JWTAuth::fromUser($user);
        $user->is_verified = true;
        $user->save();
        return $token;
    }

    /**
     * @param void
     * @return void
     *
     */
    private function createUser()
    {
        //disable laravel default error message for the test case
        $this->withoutExceptionHandling();

        // seed values
        if (Role::first() === null) {
            $this->seedRole();
        }

        //set user's data
        $user = $this->userData();
        //post data to registration end-point
        $this->post('/api/v1/register', $user); // second user (normal user)
    }

    /**
     * @param void
     * @return void
     *
     */
    public function retrieveEnrollee()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        $token    = $this->authenticateUser();
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_verify_enrollee_and_create_subscription()
    {
        $this->retrieveEnrollee();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->post('/api/v1/users/enrollees/verify', ['status' => true]);
        $response->assertStatus(200);
        $this->assertEquals(true, Enrollee::first()->is_verified);
        $response->assertJsonFragment([
            'message' => 'Verification successful'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_book_a_hospital_appointment_for_non_enrollee()
    {
        $this->createUser();
        $token = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));

        $response->assertStatus(403);
        $response->assertJsonFragment([
            'status' => FALSE,
            'message' => 'You are not an active enrollee'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_book_a_hospital_appointment()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));
        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointment created successfully', $response->json()['message']);
    }

     /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_book_a_hospital_appointment_for_dependent()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', $this->appointmentData(null));
        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals(1, HospitalAppointment::count());
        $this->assertEquals(1, DependentRequest::count());
        $this->assertEquals('hospital appointment created successfully', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_reschedule_a_hospital_appointment()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book a hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));

        // reschedule a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id, ['appointment_date' => $upcoming_date]);

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointment rescheduled successfully', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_reschedule_an_approved_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        $hospital_appointment = HospitalAppointment::first();
        $hospital_appointment->status = 'approved';
        $hospital_appointment->save();

        // reschedule a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id, ['appointment_date' => $upcoming_date]);

        $response->assertStatus(400);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('You cannot reschedule an approved appointment.', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_reschedule_a_declined_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        $hospital_appointment = HospitalAppointment::first();
        $hospital_appointment->status = 'declined';
        $hospital_appointment->save();

        // reschedule a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id, ['appointment_date' => $upcoming_date]);

        $response->assertStatus(400);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('You cannot reschedule a declined appointment.', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_fail_to_reschedule_a_non_existent_hospital_appointment()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book a hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));

        // reschedule a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->patch('/api/v1/hmo/appointments/' . 20, ['appointment_date' => $upcoming_date]);

        $response->assertStatus(404);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('hospital appointment not found', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_all_upcoming_hospital_appointments()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book an appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData($upcoming_date), ['dependent_code' => null]));

        // view all hospital appointments
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hmo/appointments/upcoming');

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointments retrieved successfully', $response->json()['message']);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_all_past_hospital_appointments()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', $current_unix_time - (86400 * 2)); // current date - 2 days

        // book an appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData($upcoming_date), ['dependent_code' => null]));

        // view all hospital appointments
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hmo/appointments/past');

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointments retrieved successfully', $response->json()['message']);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_return_empty_array_for_all_past_hospital_appointments_with_present_date()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book an appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData($upcoming_date), ['dependent_code' => null]));

        // view all hospital appointments
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hmo/appointments/past');

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointments retrieved successfully', $response->json()['message']);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(0, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_view_all_hospital_appointments_with_an_invalid_type()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book an appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData($upcoming_date), ['dependent_code' => null]));

        // view all hospital appointments
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hmo/appointments/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be past or upcoming'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_cancel_a_hospital_appointment()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));

        // book a hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));

        // cancel a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id);

        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointment cancelled successfully', $response->json()['message']);
        $this->assertEquals(null, HospitalAppointment::first());
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_cancel_an_approved_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days
        $hospital_appointment = HospitalAppointment::first();
        $hospital_appointment->status = 'approved';
        $hospital_appointment->save();
        // cancel a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id);
        $response->assertStatus(400);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('You cannot cancel an approved appointment.', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_cancel_a_declined_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days
        $hospital_appointment = HospitalAppointment::first();
        $hospital_appointment->status = 'declined';
        $hospital_appointment->save();
        // cancel a hospital appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/hmo/appointments/' . HospitalAppointment::first()->id);

        $response->assertStatus(400);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('You cannot cancel a declined appointment.', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_fail_to_cancel_a_non_existent_hospital_appointment()
    {
        $this->should_cancel_a_hospital_appointment();
        $token    = JWTAuth::fromUser(User::first());
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days

        // book a hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/appointments', array_merge($this->appointmentData(null), ['dependent_code' => null]));

        $hospital_appointment_id = HospitalAppointment::first()->id;

        // cancel a hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/hmo/appointments/' . $hospital_appointment_id);

        // duplicate cancel operation
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/v1/hmo/appointments/' . $hospital_appointment_id);

        $response->assertStatus(404);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('hospital appointment not found', $response->json()['message']);
        $this->assertEquals(null, HospitalAppointment::first());
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_request_for_drug_refill()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/drug-refills', ['reason' => 'not available in hospitals', 'drug_name' => 'panadol']);

        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('drug refill request created successfully', $response->json()['message']);
    }

     /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_request_for_drug_refill_for_dependent()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/drug-refills', 
        [
            'reason' => 'not available in hospitals', 
            'drug_name' => 'panadol', 
            'dependent_code' => 'GDM/22/1000/B1'
        ]);
        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals(1, DrugRefill::count());
        $this->assertEquals(1, DependentRequest::count());
        $this->assertEquals('drug refill request created successfully', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_give_error_for_duplicate_request_for_drug_refill()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());

        // first request
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/drug-refills', ['reason' => 'not available in hospitals', 'drug_name' => 'panadol']);

        //duplicate request
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/hmo/drug-refills', ['reason' => 'not available in hospitals', 'drug_name' => 'panadol']);

        $response->assertStatus(409);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('you have already requested for drug refill today', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_allow_enrollee_with_active_sub_proceed_to_tele_medicine()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token    = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hmo/tele-med');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'you are an active enrollee'
        ]);
    }

    private function userData()
    {
        return [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'john@example.com',
            'password'   => '123456',
            'phone_number' => '08181856273',
        ];
    }

    private function appointmentData($appointment_date)
    {
        $appointment_date = $appointment_date === null ? new DateTime('now') : $appointment_date;
        return [
            'hospital_name' => 'Lagoon Hospital',
            'doctor_name' => 'Mr Charles',
            'appointment_date' => $appointment_date,
            'comment' => '',
            'dependent_code' => 'GDM/22/1000/B1'
        ];
    }

    private function seedRole()
    {
        DB::table('roles')->insert([
            [
                'name' => 'user',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'name' => 'admin',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')

            ]
        ]);
    }

    private function seedInsuranceCalculatorDetails($type, $sex)
    {
        DB::table('insurance_demographics')->insert([
            [
                'name' => 'Age Range',
                'type' => $type,
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => $sex,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        DB::table('insurance_benefits')->insert([
            [
                'name' => 'Ambulance Service',
                'type' => $type,
                'value' => json_encode(['No' => 0, 'Yes' => 2]),
                'sex' => $sex,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ] 
        ]);
    }

    public function test_create_comment(){
        $comments= new Comment;
        $comments->user_id = $request->$user_id;
        $comments->comment = $request->$comment;
        $comments->save();
    }

}
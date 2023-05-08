<?php

namespace Tests\Feature;

use App\Models\DrugRefill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Enrollee;
use App\Models\User;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\HospitalAppointment;
use App\Models\EnrolleeRequestCard;
use App\Models\EnrolleeRequestCode;
use App\Models\HealthInsurance;
use App\Models\SubscriptionHistory;
use App\Models\HealthCareAppointment;
use App\Models\HealthCareService;
use App\Models\Hospital;
use App\Models\Service;
use App\Models\HealthServiceProviders;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use JWTAuth;
use DateTime;
use Tymon\JWTAuth\Facades\JWTAuth as FacadesJWTAuth;

class AdminManagementTest extends TestCase
{
    private $code = "GDM/22/1000/A";
    use RefreshDatabase;

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_create_health_insurance()
    {
        $user_token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData());

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'health insurance subscription created successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_create_health_insurance_for_existing_enrollee_with_subscription()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData());

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'health insurance subscription created successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_verify_existing_enrollee_and_create_subscription()
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
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_enrollee_plan_benefits()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully',
            'status' => true,
        ]);
        $this->assertNotNull($response->json()['data']['enrollee_details']['benefits']);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_get_enrollee_plan_benefits()
    {
        $current_unix_time = strtotime(date('Y-m-d'));
        $new_end_date = date('Y-m-d', (86400 * 364) + $current_unix_time);

        $this->createUser();
        $newUser = User::first();
        $newEnrollee = Enrollee::create($this->getFakeEnrollee($newUser->id));
        $newEnrollee->save();

        $new_subscription = Subscription::create([
            'user_id' => User::first()->id,
            'plan_name' => 'GUARD',
            'status' => 'active',
            'start_date' => date('Y-m-d'),
            'end_date' => $new_end_date
        ]);
        $new_subscription->save();

        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(200);
        //assert that benefits are not retrieved
        $response->assertJsonFragment([
            'status' => true,
            'benefits' => null
        ]);
    }



    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_all_verified_enrollees_with_an_active_or_inactive_subscription_status()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Enrollees retrieved successfully',
            'status' => true
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_all_verified_enrollees_with_a_pending_subscription()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees?page_items=10&page=1&subscription_status=pending');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Enrollees retrieved successfully',
            'status' => true
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_single_legacy_enrollee_by_id()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Enrollee records retrieved successfully'
        ]);
        $this->assertIsArray($response->json()['data']);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_single_custom_enrollee_by_id()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Enrollee records retrieved successfully'
        ]);
        $this->assertIsArray($response->json()['data']);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_get_single_custom_enrollee_by_id_for_non_existent_health_insurance()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();

        // delete health insurance object
        $health_insurance = HealthInsurance::first();
        $health_insurance->delete();

        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'health insurance not found'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_get_single_enrollee_with_invalid_id_for_legacy_enrollee()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/' . 201);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'enrollee not found'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_retrieve_non_enrollees()
    {
        $this->retrieveEnrollee();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/non-enrollees/');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'non-enrollees retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_accept_pending_enrollees()
    {
        $this->should_create_health_insurance();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/enrollees/' . Enrollee::first()->id, ['enrollee_id' => 'abcd']);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'enrollee accepted successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_accept_pending_enrollees_for_non_existent_health_insurance_object()
    {
        $this->should_create_health_insurance();
        $admin_token = $this->authenticateAdmin();

        // delete health insurance object
        $health_insurance = HealthInsurance::first();
        $health_insurance->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/enrollees/' . Enrollee::first()->id, ['enrollee_id' => 'abcd']);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'health insurance not found'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_accept_pending_enrollees_for_non_existent_subscription_history_object()
    {
        $this->should_create_health_insurance();
        $admin_token = $this->authenticateAdmin();

        // delete health insurance object
        $subscription_history = SubscriptionHistory::first();
        $subscription_history->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/enrollees/' . Enrollee::first()->id, ['enrollee_id' => 'abcd']);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'subscription history not found'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_accept_pending_enrollees_for_an_invalid_user()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/enrollees/' . 50, ['enrollee_id' => 'abcd']);

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'enrollee not found'
        ]);
    }

    /**
     *@test
     */
    public function should_register_enrollee_new_card_request()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::where('role_id', 1)->first()),
        ])->post('/api/v1/users/enrollees/request-card', ['card_collected' => FALSE, 'passport_url' => 'http://passporturl.com']);
        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals('card request was successful', $response->json()['message']);
    }




    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_pending_card_requests()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-card/' . 'pending');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'card requests retrieved successfully'
        ]);
    }


    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_approved_card_requests()
    {
        $this->should_approve_card_request();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-card/' . 'approved');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'card requests retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_declined_card_requests()
    {
        $this->should_decline_card_request();
        $admin_token = $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-card/' . 'declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'card requests retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_retrieve_card_requests_with_incorrect_status()
    {
        $this->should_decline_card_request();
        $admin_token = $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-card/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved, pending or declined'
        ]);
    }


    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_approve_card_request()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $cardRequest = EnrolleeRequestCard::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-card/' . $cardRequest->id . '/' .  'approved');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'card request approved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_decline_card_request()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $cardRequest = EnrolleeRequestCard::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-card/' . $cardRequest->id . '/' . 'declined');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'card request declined successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_card_request_with_an_incorrect_status()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $cardRequest = EnrolleeRequestCard::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-card/' . $cardRequest->id . '/' . 'nonsense');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be approved or declined'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_card_request_with_invalid_id()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $cardRequest = EnrolleeRequestCard::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-card/' . 100 . '/' . 'approved');
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'card request not found'
        ]);
    }

    /**
     * Admin feature test.
     * @test
     * @return void
     * 
     */
    public function should_view_single_card_request_by_id()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/request-card/' . EnrolleeRequestCard::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'caard request details retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     * @test
     * @return void
     * 
     */
    public function should_not_view_single_card_request_with_invalid_id()
    {
        $this->should_register_enrollee_new_card_request();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/request-card/67#1');
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'card request not found'
        ]);
    }


    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_request_for_drug_refill()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::where('email', 'a.emmanuel2@yahoo.com')->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token,
        ])->post('/api/v1/hmo/drug-refills', ['reason' => 'not available in hospitals', 'drug_name' => 'panadol']);
        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('drug refill request created successfully', $response->json()['message']);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_approve_drug_refill()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/drug-refills/' . DrugRefill::first()->id . '/approved');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill approved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_decline_drug_refill()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/drug-refills/' . DrugRefill::first()->id . '/declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill declined successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_approve_drug_refill_for_non_existent_user()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/drug-refills/' . 50 . '/approved');

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'drug refill request not found'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_approve_or_decline_drug_refill_for_incorrect_status()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/drug-refills/' .  DrugRefill::first()->id . '/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be approved or declined'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_view_approved_drug_refill_requests()
    {
        $this->should_approve_drug_refill();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/drug-refills/approved');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill requests retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_view_declined_drug_refill_requests()
    {
        $this->should_decline_drug_refill();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/drug-refills/declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill requests retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_view_pending_drug_refill_requests()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/drug-refills/pending');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill requests retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_view_drug_refill_requests_with_an_incorrect_status()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/drug-refills/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved, pending or declined'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_view_single_drug_refill_by_id()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/drug-refills/' . DrugRefill::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'drug refill details retrieved successfully'
        ]);
    }


    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_view_single_drug_refill_with_invalid_id()
    {
        $this->should_request_for_drug_refill();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/drug-refills/' . 20000);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'drug refill not found'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_book_a_hospital_appointment()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::where('email', 'a.emmanuel2@yahoo.com')->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token,
        ])->post('/api/v1/hmo/appointments', $this->appointmentData());

        $response->assertStatus(201);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('hospital appointment created successfully', $response->json()['message']);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_approve_a_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/hospital-appointments/' . HospitalAppointment::first()->id . '/approved');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointment approved successfully'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_decline_a_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/hospital-appointments/' . HospitalAppointment::first()->id . '/declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointment declined successfully'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_a_hospital_appointment_for_a_non_existent_user()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/hospital-appointments/' . 50 . '/declined');

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'hospital appointment not found'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_a_hospital_appointment_for_an_incorrect_status()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/hospital-appointments/' . HospitalAppointment::first()->id . '/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be approved or declined'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_approved_hospital_appointments()
    {
        $this->should_approve_a_hospital_appointment();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/hospital-appointments/approved');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointments retrieved succesfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_declined_hospital_appointments()
    {
        $this->should_decline_a_hospital_appointment();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/hospital-appointments/declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointments retrieved succesfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_pending_hospital_appointments()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/hospital-appointments/pending');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointments retrieved succesfully'
        ]);

        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_fail_to_view_hospital_appointments_with_an_incorrect_status()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/hospital-appointments/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved, pending or declined'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_view_single_hospital_appointment()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/hmo/appointments/' . HospitalAppointment::first()->id);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'hospital appointment details retrieved successfully'
        ]);
    }

    /**
     * Hospital appointment feature test
     *@test
     * @return void
     */
    public function should_not_view_single_hospital_appointment_with_invalid_id()
    {
        $this->should_book_a_hospital_appointment();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/hmo/appointments/20009998');

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'hospital appointment not found'
        ]);
    }

    /**
     *@test
     * @return void
     */
    public function should_retrieve_dashboard_data()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::where('role_id', 1)->first());

        // seed required data

        // hospital appointment
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token,
        ])->post('/api/v1/hmo/appointments', $this->appointmentData());
        // drug refill
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token,
        ])->post('/api/v1/hmo/drug-refills', ['reason' => 'not available in hospitals', 'drug_name' => 'panadol']);
        // card request
        $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::where('role_id', 1)->first()),
        ])->post('/api/v1/users/enrollees/request-card', ['card_collected' => FALSE, 'passport_url' => 'http://passporturl.com']);

        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/dashboard');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'data retrieved successfully'
        ]);
    }

    /**
     * @test
     * Test login
     */
    public function should_log_in_user_with_admin_role()
    {
        $this->createAdmin();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //sttempt to post to the validate end-point
        $response = $this->post('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => 'password']);
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert type key exist
        $this->assertArrayHasKey('type', $response->json());
        //assert payload key exist
        $this->assertArrayHasKey('payload', $response->json());
        //assert token key exist
        $this->assertArrayHasKey('token', $response->json());
        //assert message is 'Login was successful'
        $this->assertEquals('Login was successful', $response->json()['message']);
        //assert status true
        $this->assertEquals(true, $response->json()['status']);
        //assert status code is 200
        $response->assertStatus(200);
    }


    /**
     * @test
     * Test login
     */
    public function should_not_log_in_user_without_admin_role()
    {
        $this->createUser();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //sttempt to post to the validate end-point
        $response = $this->post('/api/v1/admin/login', ['email' => 'a.emmanuel2@yahoo.com', 'password' => 'password']);
        $this->assertEquals('You are forbidden from viewing this resource', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
        //assert status code is 403
        $response->assertStatus(403);
    }

    /**
     * @test
     * Test login
     */
    public function should_not_log_in_admin_with_invalid_password()
    {
        $this->createAdmin();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //sttempt to post to the validate end-point
        $response = $this->post('/api/v1/admin/login', ['email' => 'admin@example.com', 'password' => '123456']);
        $this->assertEquals('Login failed', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
        $response->assertStatus(400);
    }

    /**
     *@test
     */
    public function should_register_enrollee_request_code()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->post('/api/v1/users/enrollees/request-code', $this->requestCodeData());
        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals('code request was successful', $response->json()['message']);
    }




    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_pending_code_requests()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-code/' . 'pending');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'code requests retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_retrieve_code_requests_with_incorrect_status()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-code/nonsense');

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved, pending or declined'
        ]);
    }


    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_approved_code_requests()
    {
        $this->should_approve_code_request();
        $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-code/' . 'approved');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'code requests retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_declined_code_requests()
    {
        $this->should_decline_code_request();
        $admin_token = $admin_token = JWTAuth::fromUser(User::where('role_id', 2)->first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/request-code/' . 'declined');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'code requests retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_approve_code_request()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $codeRequest = EnrolleeRequestCode::first();
        $this->assertNull(EnrolleeRequestCode::first()->request_code);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-code/' . $codeRequest->id . '/' . 'approved', ['request_code' => '123456']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'code request approved successfully'
        ]);
        $this->assertNotNull(EnrolleeRequestCode::first()->request_code);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_approve_code_request_with_a_null_request_code_value()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $codeRequest = EnrolleeRequestCode::first();
        $this->assertNull(EnrolleeRequestCode::first()->request_code);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-code/' . $codeRequest->id . '/' . 'approved', ['request_code' => null]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'request code field is required'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_decline_code_request()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $codeRequest = EnrolleeRequestCode::first();
        $this->assertNull(EnrolleeRequestCode::first()->request_code);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-code/' . $codeRequest->id . '/' . 'declined');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'code request declined successfully'
        ]);
        $this->assertNull(EnrolleeRequestCode::first()->request_code);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_code_request_with_an_incorrect_status()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $codeRequest = EnrolleeRequestCode::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-code/' . $codeRequest->id . '/' . 'nonsense');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be approved or declined'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_approve_or_decline_code_request_with_invalid_id()
    {
        $this->should_register_enrollee_request_code();
        $admin_token = $this->authenticateAdmin();
        $cardRequest = EnrolleeRequestCode::first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v1/admin/request-code/' . 1234567 . '/' . 'approved', ['request_code' => '123456']);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'auth code request not found'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_request_code_by_id()
    {
        $this->should_register_enrollee_request_code();
        $this->assertEquals(1, EnrolleeRequestCode::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/request-code/' . EnrolleeRequestCode::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'auth code request details retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_get_request_code_with_invalid_id()
    {
        $this->should_register_enrollee_request_code();
        $this->assertEquals(1, EnrolleeRequestCode::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/enrollees/request-code/' . 124567);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'auth code request not found'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_non_enrollee_by_id()
    {
        $this->createUser();
        $this->assertEquals(1, User::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/non-enrollees/' . User::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'user details retrieved successfully'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_non_enrollee_with_invalid_id()
    {
        $this->createUser();
        $this->assertEquals(1, User::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v1/admin/non-enrollees/' . 45059);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'user not found'
        ]);
    }

     /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_all_users()
    {
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/users');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'All Users retrieved successfully',
            'status' => true
        ]);
    }
    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_user_by_id()
    {
        $this->createUser();
        $this->assertEquals(1, User::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/users/' . User::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'User details retrieved successfully'
        ]);
    }
    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_user_with_invalid_id()
    {
        $this->createUser();
        $this->assertEquals(1, User::count());
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/users/' . 450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'User not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_dental_appointments()
    {
        $type = 'dental';
        $status = 'approved';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health care appointments retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_optical_appointments()
    {
        $type = 'optical';
        $status = 'approved';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health care appointments retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_comprehensive_appointments()
    {
        $type = 'comprehensive';
        $status = 'approved';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health care appointments retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_retrieve_health_care_appointments_for_invalid_status()
    {
        $type = 'dental';
        $status = 'approvedss';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved or pending'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_retrieve_health_care_appointments_for_invalid_type()
    {
        $type = 'dentalss';
        $status = 'approved';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be: dental, optical, comprehensive, pre-employment, cancer or basic-health-check'
        ]);
    }

    // Should retrieve all appointments for a specific user
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_retrieve_valid_dental_appointments()
    {
        $type = 'dental';
        $status = 'approved';
        $page_items = null;
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointments/' . $type . '/' . $status, ['page_items' => $page_items]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health care appointments retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_get_appointment_details_for_invalid_id()
    {
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten healthcare appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointment-details/' . 450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Appointment not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_appointment_details_by_id()
    {
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten healthcare appointments with factory
        factory(HealthCareAppointment::class, 1)->create();
        // Create ten healthcare services with factory
        factory(HealthCareService::class, 1)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/appointment-details/' . HealthCareAppointment::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment details retrieved successfully'
        ]);
    }
     /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_update_appointment_request_with_incorrect_status()
    {
        $status = 'approvedss';
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/appointment/request-code/' . HealthCareAppointment::first()->id . '/' . $status, ['request_code' => '123456']);
        
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'status must be: approved or pending'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_update_appointment_for_invalid_id()
    {
        $status = 'approved';
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten healthcare appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/appointment/request-code/' . 450590 . '/' . $status, ['request_code' => '123456']);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Appointment not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_update_appointment_with_a_null_request_code_value()
    {
        $status = 'approved';
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten healthcare appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/appointment/request-code/' . HealthCareAppointment::first()->id . '/' . $status, ['request_code' => '']);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'request code field is required'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_update_appointment_status_by_id()
    {
        $status = 'approved';
        $this->seedRole();
        // Create five users with factory
        factory(User::class, 5)->create();
        // Create ten healthcare appointments with factory
        factory(HealthCareAppointment::class, 10)->create();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/appointment/request-code/' . HealthCareAppointment::first()->id . '/' . $status, ['request_code' => '123456']);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment status updated successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_all_hospitals()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/settings/hospitals');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospitals retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_get_hospital_by_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/settings/hospitals/' .  Hospital::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital retrieved successfully'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_get_hospital_with_invalid_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/settings/hospitals/' .  450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Hospital not found'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_retrieve_instant_care_dashboard_data()
    {
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/dashboard');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Dashboard data retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_get_all_hospitals_for_admin()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/hospitals');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospitals retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_get_all_hospital_levels()
    {
        $this->seedHospitalLevels();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/hospital-levels');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital levels retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_create_hospital()
    {        
        $hospital = $this->hospitalData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->post('/api/v2/admin/hospitals', $hospital);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital created successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_show_hospital_with_invalid_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/hospitals/' .  450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Hospital not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_show_hospital_by_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/hospitals/' .  Hospital::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_update_hospital_with_invalid_id()
    {
        $this->seedHospitals();
        $hospital = $this->hospitalData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/hospitals/' . 450590, $hospital);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Hospital not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_update_hospital_with_valid_id()
    {
        $this->seedHospitals();
        $hospital = $this->hospitalData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/hospitals/' . Hospital::first()->id, $hospital);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital updated successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_delete_hospital_with_invalid_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/hospitals/' . 450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Hospital not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_delete_hospital_with_valid_id()
    {
        $this->seedHospitals();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/hospitals/' . Hospital::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Hospital deleted successfully'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_get_all_services()
    {
        $this->seedServices();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/services');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Services retrieved successfully'
        ]);
    }
   /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_create_service()
    {        
        $service = $this->serviceData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->post('/api/v2/admin/services', $service);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Service created successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_show_service_with_invalid_id()
    {
        $this->seedServices();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/services/' .  450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Service not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_show_service_by_id()
    {
        $this->seedServices();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/services/' .  Service::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Service retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_update_service_with_invalid_id()
    {
        $this->seedServices();
        $service = $this->serviceData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/services/' . 450590, $service);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Service not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_update_service_with_valid_id()
    {
        $this->seedServices();
        $service = $this->serviceData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/services/' . Service::first()->id, $service);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Service updated successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_delete_service_with_invalid_id()
    {
        $this->seedServices();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/services/' . 450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Service not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_delete_service_with_valid_id()
    {
        $this->seedServices();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/services/' . Service::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Service deleted successfully'
        ]);
    }
    /**
     * Admin feature test.
     * @test
     * @return void
     */
    public function should_get_all_health_service_providers()
    {
        $this->seedHealthServiceProviders();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/health-service-providers');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health service providers retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_create_health_service_providers()
    {
        $health_service_provider = $this->healthServiceProviderData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->post('/api/v2/admin/health-service-providers', $health_service_provider);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health service provider created successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_show_health_service_provider_with_invalid_id()
    {
        $this->seedHealthServiceProviders();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/health-service-providers/' .  450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Health service provider not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_show_health_service_provider_by_id()
    {
        $this->seedHealthServiceProviders();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->get('/api/v2/admin/health-service-providers/' .  HealthServiceProviders::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health service provider retrieved successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_update_health_service_provider_with_invalid_id()
    {
        $this->seedHealthServiceProviders();
        $health_service_provider = $this->healthServiceProviderData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/health-service-providers/' . 450590, $health_service_provider);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Health service provider not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_update_health_service_provider_with_valid_id()
    {
        $this->seedHealthServiceProviders();
        $health_service_provider = $this->healthServiceProviderData();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->patch('/api/v2/admin/health-service-providers/' . HealthServiceProviders::first()->id, $health_service_provider);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health service provider updated successfully'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_not_delete_health_service_provider_with_invalid_id()
    {
        $this->seedHealthServiceProviders();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/health-service-providers/' . 450590);
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Health service provider not found'
        ]);
    }
    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_delete_health_service_provider_with_valid_id()
    {
        $this->seedHealthServiceProviders();
        $admin_token = $this->authenticateAdmin();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $admin_token,
        ])->delete('/api/v2/admin/health-service-providers/' . HealthServiceProviders::first()->id);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Health service provider deleted successfully'
        ]);
    }
    /**
     *@param void
     *@return token
     */
    public function authenticateAdmin()
    {
        $this->createAdmin();
        $token = JWTAuth::fromUser(User::where('email', 'admin@example.com')->first());
        return $token;
    }

    /**
     *@param void
     *@return token
     */
    public function authenticateUser()
    {
        $this->createUser();
        $user = User::where('email', 'a.emmanuel2@yahoo.com')->first();
        $token = JWTAuth::fromUser($user);
        $user->is_verified = true;
        $user->save();
        return $token;
    }


    /**
     * @param void
     * @return void
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


    private function createAdmin()
    {
        if (Role::first() === null) {
            $this->seedRole();
        }

        $this->seedAdmin();
    }

    private function getAdmin()
    {
        $admin = User::first();
        return $admin;
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
        $user = $this->data();
        //post data to registration end-point
        $this->post('/api/v1/register', $user); // second user (normal user)
    }
    /**
     * @param void
     * @return data
     */
    private function data()
    {
        return
            [
                'first_name' => 'John',
                'last_name'  => 'Doe',
                'email'      => 'a.emmanuel2@yahoo.com',
                'password'   => '123456',
                'phone_number' => '+2348181856273',
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

    private function seedAdmin()
    {
        DB::table('users')->insert([
            'role_id' => 2,
            'first_name' => 'super',
            'last_name' => 'admin',
            'email' => 'admin@example.com',
            'phone_number' => '08012345678',
            'is_blocked' => false,
            'is_verified' => true,
            'password' => Hash::make('password'),
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
        ]);
    }

    private function appointmentData()
    {
        return [
            'hospital_name' => 'Lagoon Hospital',
            'doctor_name' => 'Mr Charles',
            'appointment_date' => new DateTime('now'),
            'comment' => ''
        ];
    }

    private function getFakeEnrollee($user_id)
    {
        $faker = Faker::create();
        return [
            'user_id' => $user_id,
            'enrollee_id' => Str::random(10),
            'company' => $faker->company(),
            'email' => $faker->unique()->safeEmail(),
            'phone_number' => $faker->phoneNumber(),
            'hospital_name' => $faker->city,
            'is_verified' => true,
            'plan' => $faker->text(),
            'name' => $faker->text(),
        ];
    }

    private function requestCodeData()
    {
        return [
            'user_id' => 1,
            'enrollee_id' => 'ojkQaZZkle',
            'hospital_name'  => 'this is the hospital name',
            'request_message'      => 'requesting for a message',
            'request_code'   => null,
        ];
    }

    private function healthInsuranceData()
    {
        return [
            'type' => 'single',
            'sex' => 'male',
            'demographics' => [
                'fields' => ['Age Range'],
                'values' => ['18-29']
            ],
            'benefits' => [
                'fields' => ['Ambulance Service'],
                'values' => ['Yes']
            ],
            'transaction_id' => 'test_123456',
            'amount_paid' => 2000,
            'hospital' => 'lagos hospital'
        ];
    }
    
    private function seedHospitals()
    {
        DB::table('hospitals')->insert([
           [
            'name' => 'NAVAL MEDICAL CENTRE',
            'address' => 'AHMADU BELLO WAY',
            'location' => 'VICTORIA ISLAND',
            'plan' => 'guard',
            'level' => '1',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'DR ABIMBOLA MEMORIAL HOSPITAL',
            'address' => '183, BAMGBOSE STREET',
            'location' => 'LAGOS ISLAND',
            'plan' => 'guard',
            'level' => '1',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'DOVE HOSPITAL AND DIAGNOSTIC CENTRE',
            'address' => 'BADORE ROAD ,ADEWALE B/STOP,AJAH',
            'location' => 'AJAH',
            'plan' => 'guard',
            'level' => '1',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'FIRST CITY HOSPITAL',
            'address' => '17, OSAPA ROAD, OSAPA-EPE EXPRESSWAY',
            'location' => 'LEKKI',
            'plan' => 'guard',
            'level' => '1',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
        ]);
    }
    private function seedHospitalLevels()
    {
        DB::table('hospital_levels')->insert([
           [
            'name' => 'guard',
            'level' => '1',
            'point' => '2',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'guard 1',
            'level' => '2',
            'point' => '3',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'guard 2',
            'level' => '3',
            'point' => '4',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'shield',
            'level' => '4',
            'point' => '5',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'shield 1',
            'level' => '5',
            'point' => '6',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'premium',
            'level' => '6',
            'point' => '7',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'premium 1',
            'level' => '7',
            'point' => '8',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'premium 2',
            'level' => '8',
            'point' => '9',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'exclusive',
            'level' => '9',
            'point' => '20',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
           [
            'name' => 'special',
            'level' => '10',
            'point' => '1',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
           ],
        ]);
    }
    /**
    * @param void
    * @return data
    */
    private function hospitalData()
    {
        return
        [
            'id' => 1,
            'name' => 'Boju Boju Hospital',
            'address'  => '10, Boju Olu Crescent, Ilasamaja, Ikotun, Lagos',
            'location'      => 'Ikotun',
            'plan'   => 'special',
            'level' => '10',
        ];
    }
    /**
    * @param void
    * @return data
    */
    private function serviceData()
    {
        return
        [
            'id' => 1,
            'name' => 'dental',
            'created_at' => date('Y/m/d'),
            'updated_at' => date('Y/m/d')
        ];
    }
    private function seedServices()
    {
        DB::table('services')->insert([
            [
                'name' => 'dental',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
            [
                'name' => 'optical',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],           
            [
                'name' => 'comprehensive',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
    }
    /**
    * @param void
    * @return data
    */
    private function seedHealthServiceProviders()
    {
        DB::table('health_service_providers')->insert([
            [
                'location' => 'Ikotun',
                'name' => 'Dr. A.B.O.L.A',
                'address' => '10, Boju Olu Crescent, Ilasamaja, Ikotun, Lagos',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d'),
                'service_id' => 1
            ],
            [
                'location' => 'Gbagada-Shomolu',
                'name' => 'Rjolad Hospital Dental Clinic',
                'address' => '1, Akindele Street, near new garage bus stop, Bariga, Gbagada',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d'),
                'service_id' => 2
            ],
            [
                'location' => 'Port Harcourt',
                'name' => 'AIRE DENTAL CLINC',
                'address' => '52, PH/ABA Express Way, Artillery Junction, Port-Harcourt',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d'),
                'service_id' => 3
            ],
        ]);            
            
    }
    private function healthServiceProviderData()
    {
        return
        [
            'id' => 1,
            'name' => 'Boju Boju Hospital',
            'address'  => '10, Boju Olu Crescent, Ilasamaja, Ikotun, Lagos',
            'location'      => 'Ikotun',
            'service_id'   => 1,
        ];
    }
          
}
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Enrollee;
use App\Models\Hospital;
use App\Models\HealthInsurance;
use App\Models\User;
use App\Models\Role;
use App\Models\Subscription;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use JWTAuth;

class HospitalManagementTest extends TestCase
{

    // private $code = "HIL/17/2/A";
    private $code = "HTC/21/1/A";
    use RefreshDatabase;

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
    public function should_create_health_insurance_for_existing_enrollee_with_subscription(){
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData('Guard 1'));

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'health insurance subscription created successfully'
        ]);

    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_all_hospital_locations_for_legacy_enrollees()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(1, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_all_hospital_locations_for_custom_enrollees()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $token = JWTAuth::fromUser(User::first());
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_fail_to_get_all_hospital_locations_for_custom_enrollees_for_non_existent_health_insurnce()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $token = JWTAuth::fromUser(User::first());

        // delete health insurance object
        $health_insurance = HealthInsurance::first();
        $health_insurance->delete();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations');

        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'health insurance not found'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_hospital_by_location_for_legacy_enrollees()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations/LEKKI');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Hospitals retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(1, $count);
    }
    
    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_hospital_by_location_for_legacy_enrollees_for_non_existent_location()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations/IKEJA');
        
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => 'Hospitals not found within that location for your insurance plan',
            'status' => false
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_hospital_by_location_for_custom_enrollees()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $token = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations/LEKKI');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Hospitals retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_fail_to_get_hospital_by_location_for_custom_enrollees_for_non_existent_health_insurance()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $token = JWTAuth::fromUser(User::first());

         // delete health insurance object
         $health_insurance = HealthInsurance::first();
         $health_insurance->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/enrollees/locations/LEKKI');
        
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'health insurance not found'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_all_hospital_locations_for_non_enrollees()
    {
        $token = $this->authenticateUser();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/locations');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_hospital_by_location_for_non_enrollees()
    {
        $token = $this->authenticateUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/hospital/locations/LEKKI');
        
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Hospitals retrieved successfully',
            'status' => true
        ]);

        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_health_centre_locations_for_dental_service()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/dental');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);
        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

     /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_health_centre_locations_for_optical_service()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/optical');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);
        $count = $response->json()['data']['count'];
        $this->assertEquals(3, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_health_centre_locations_for_comprehensive_service()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/comprehensive');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Locations retrieved successfully',
            'status' => true
        ]);
        $count = $response->json()['data']['count'];
        $this->assertEquals(1, $count);
    }

     /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_health_centre_locations_for_invalid_service_name()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/bad_query_param');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'type must be dental, optical or comprehensive',
            'status' => false
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_health_centres_by_location()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/dental/LEKKI');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Health centres retrieved successfully',
            'status' => true
        ]);
        $count = $response->json()['data']['count'];
        $this->assertEquals(2, $count);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_health_centres_by_location_for_invalid_service_name()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/bad_query_param/LEKKI');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'type must be dental, optical or comprehensive',
            'status' => false
        ]);
    }

     /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_get_health_centres_by_location_for_invalid_location_name()
    {
        $this->seedHealthCentre();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/health-centre/locations/dental/Bariga');
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => 'We could not find health centres or hospitals within Bariga',
            'status' => false
        ]);
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
     *@param void
     *@return token
     */
    public function authenticate()
    {
        $this->createUser();
        $token = JWTAuth::fromUser(User::first());
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
     *
     */
    private function createUser()
    {
        //disable laravel default error message for the test case
        $this->withoutExceptionHandling();

        // seed values
        $this->seedRole();
        $this->seedHospital();

        //set user's data
        $user = $this->data();
        //post data to registration end-point
        $this->post('/api/v1/register', $user); // second user (normal user)
    }

    private function getInActiveSubscription()
    {
        return [
            'user_id' => 1,
            'plan_name' =>'GUARD',
            'status' => 'active',
            'start_date' => '2018-01-01',
            'end_date' => '2018-12-31',
            ];
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
                'phone_number' => '08181856273',
            ];
    }

    private function seedRole(){
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

    private function healthInsuranceData($choice_of_hospital){
        return [
            'type' => 'single',
            'sex' => 'male',
            'demographics' => [
                'fields' => ['Age Range', 'Choice of Hospital'],
                'values' => ['18-29', $choice_of_hospital]
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

    private function hosiptalSeederHelper($name, $location, $plan, $level){
        return [
            'name' => $name,
            'address' => 'hospital address',
            'location' => $location,
            'plan' => $plan,
            'level' => $level
        ];
    }
    private function seedHospital(){
        // seed hospital levels
        DB::table('hospital_levels')->insert([
            [
                'name' => 'guard',
                'level' => 1,
                'point' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'guard 1',
                'level' => 2,
                'point' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        DB::table('hospitals')->insert([
            $this->hosiptalSeederHelper('first hospital', 'LEKKI', 'guard', 1),
            $this->hosiptalSeederHelper('second hospital', 'IKEJA', 'guard 1', 2),
            $this->hosiptalSeederHelper('third hospital', 'LEKKI', 'guard 1', 2)
        ]);
    }
    private function healthCentreSeederHelper($location, $name, $address, $service_id){
        return [
            'name' => $name,
            'address' => $address,
            'location' => $location,
            'service_id' => $service_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }
    private function seedHealthCentre(){
        DB::table('services')->insert([
            [
                'name' => 'dental',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'optical',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'comprehensive',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
        DB::table('health_service_providers')->insert([
            $this->healthCentreSeederHelper('LEKKI', 'first health centre', 'address', 1),
            $this->healthCentreSeederHelper('LEKKI', 'first health centre', 'address', 1),
            $this->healthCentreSeederHelper('SOMOLU', 'first health centre', 'address', 1),
            $this->healthCentreSeederHelper('IKEJA', 'second health centre', 'address', 2),
            $this->healthCentreSeederHelper('IKEJA', 'second health centre', 'address', 2),
            $this->healthCentreSeederHelper('AGEGE', 'second health centre', 'address', 2),
            $this->healthCentreSeederHelper('OJOTA', 'second health centre', 'address', 2),
            $this->healthCentreSeederHelper('OJOTA', 'second health centre', 'address', 2),
            $this->healthCentreSeederHelper('LEKKI', 'third health centre', 'address', 3),
            $this->healthCentreSeederHelper('LEKKI', 'third health centre', 'address', 3)
        ]);

    }
}
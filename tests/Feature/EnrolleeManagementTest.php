<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\Subscription;
use App\Models\HealthInsurance;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use JWTAuth;


class EnrolleeManagementTest extends TestCase
{
    // private $code = "HIL/17/2/A";
    private $code = "HTC/21/1/A";
    use RefreshDatabase;
    
     /**
     * @test
     */
    public function retrieve_dependant_enrollee_records()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        //invoke authenticate method
        $token    = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => 'GDM/21/303/B1']);
        $response->assertStatus(200);
        $this->assertEquals(0, Enrollee::first()->is_verified);
        //assert that message is The enrollee id is valid
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_retrieve_enrollee_from_legacy_system_by_enrollee_id()
    {
        //invoke retreive enrollee method
        $this->retrieveEnrollee();
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_retrieve_verified_enrollee_from_database()
    {
        $this->should_verify_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
        $response->assertStatus(200);
        $this->assertEquals(true, Enrollee::first()->is_verified);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_retrieve_unverified_enrollee_from_database()
    {
        $this->retrieveEnrollee();
        $token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
        $response->assertStatus(200);
        $this->assertEquals(0, Enrollee::first()->is_verified);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);
    }


    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_retrieve_verified_enrollee_assigned_to_existing_user_in_database()
    {
        $this->retrieveEnrollee();
        $enrollee = Enrollee::first();
        $enrollee->is_verified = true;
        $enrollee->save();
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
        $response->assertStatus(403);
        $this->assertEquals(true, $enrollee->is_verified);
        $response->assertJsonFragment([
            'message' => 'Sorry, that enrollee id has already been assigned to an existing user'
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function should_map_unverified_enrollee_records_to_new_user_account()
    {
        $this->retrieveEnrollee();
        $this->assertEquals(User::first()->id, Enrollee::first()->user_id);
        $new_user = factory(User::class)->create();
        $this->assertNotEquals($new_user->id, Enrollee::first()->user_id);
        $token = JWTAuth::fromUser($new_user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'The enrollee id has been mapped to your account, please kindly verify to proceed',
            'enrollee_id' => $this->code
        ]);
        $this->assertEquals($this->code, Enrollee::where('user_id', $new_user->id)->first()->enrollee_id);
        $this->assertEquals($this->code, Enrollee::where('user_id', User::first()->id)->first()->enrollee_id);
        $this->assertNotNull(Enrollee::where('user_id', $new_user->id));
        $this->assertNotNull(Enrollee::where('user_id', User::first()->id));
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_update_unverified_enrollee_user_with_unverified_enrollee_records_in_database()
    {
        $this->retrieveEnrollee();
        $this->assertEquals(User::first()->id, Enrollee::first()->user_id);
        $token = JWTAuth::fromUser(User::first());
        $faker = Faker::create();
        $enrollee = factory(Enrollee::class)->create([
            'user_id' => 2,
            'enrollee_id'=>Str::random(10),
            'company'=>$faker->company(),
            'email'=>$faker->unique()->safeEmail(),
            'phone_number'=>$faker->phoneNumber(),
            'hospital_name'=>$faker->city,
            'is_verified'=> false,
            'plan'=> $faker->randomElement(['guard', 'shield', 'premium', 'exclusives', 'custom', 'special']),
            'name'=>$faker->text(),
        ]);
        $this->assertEquals($this->code, Enrollee::where('user_id', User::first()->id)->first()->enrollee_id);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $enrollee->enrollee_id]);
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertArrayHasKey('enrollee_id', $response->json()['data']);
        $response->assertJsonFragment([
            'message' => 'The enrollee id has been mapped to your account, please kindly verify to proceed',
            'enrollee_id' => $enrollee->enrollee_id
        ]);
        $this->assertNotEquals($this->code, Enrollee::where('user_id', User::first()->id)->first()->enrollee_id);
        $this->assertEquals($enrollee->enrollee_id, Enrollee::where('user_id', User::first()->id)->first()->enrollee_id);
    }


    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_forbid_verified_enrollee_user_from_retrieving_enrollee_record_from_legacy_system()
    {
        $this->retrieveEnrollee();
        $token = JWTAuth::fromUser(User::first());
        $enrollee = Enrollee::first();
        $enrollee->is_verified = true;
        $enrollee->save();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => 'GDM/21/303/A']);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'message' => 'That enrollee id does not belongs to you'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_update_unverify_existing_enrollee_user_with_new_enrollee_records_from_legacy_system()
    {
        $this->retrieveEnrollee();
        $this->assertNotEquals('GDM/21/303/A', Enrollee::first()->enrollee_id);
        $token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => 'GDM/21/303/A']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'The enrollee id has been mapped to your account, please kindly verify to proceed',
            'id' => Enrollee::first()->id,
            'enrollee_id' => 'GDM/21/303/A'
        ]);
        $this->assertEquals('GDM/21/303/A', Enrollee::first()->enrollee_id);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_return_an_error_for_invalid_enrollee_id()
    {
        $this->withoutExceptionHandling();
        $token    = $this->authenticate();
        $code = "shishi";
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $code]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'message' => 'That enrollee id is invalid'
        ]);
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
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_verify_an_already_verified_enrollee()
    {
        $this->retrieveEnrollee();
        // verify enrollee for the first time
        $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->post('/api/v1/users/enrollees/verify', ['status' => true]);
        // double verification
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->post('/api/v1/users/enrollees/verify', ['status' => true]);
        $response->assertStatus(409);
        $this->assertEquals(true, Enrollee::first()->is_verified);
        $response->assertJsonFragment([
            'message' => 'Enrollee has already been verified',
            'status' => false
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_verify_enrollee()
    {
        $this->retrieveEnrollee();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->post('/api/v1/users/enrollees/verify', ['status' => false]);
        $response->assertStatus(400);
        $this->assertEquals(0, Enrollee::first()->is_verified);
        $response->assertJsonFragment([
            'message' => 'Verification failed'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_forbid_inactive_enrollee_from_viewing_plan_benefits()
    {
        $this->retrieveEnrollee();
        $subscription = Subscription::create($this->getInActiveSubscription());
        $subscription->save();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/users/enrollees/hmo-plan-benefits');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Oops!, your subscription has expired, please contact your HR',
            'status' => false
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_enrollee_plan_benefits_for_legacy_user()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        //invoke authenticate method
        $token    = $this->authenticate();
        DB::table('enrollees')->insert([
            [
                'user_id' => 1,
                'enrollee_id' => 'GDM/21/303/A',
                'phone_number' => '0909090909',
                'email' => 'a.emmanuel2@yahoo.com',
                'plan' => 'guard',
                'company' => 'Wellness',
                'hospital_name' => 'Eko Hospital',
                'name' => 'Jane Steve',
                'is_verified' => true,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        DB::table('subscriptions')->insert([
            [
                'user_id' => 1,
                'plan_name' => 'guard',
                'status' => 'active',
                'start_date' => '2021-01-01',
                'end_date' => '2030-04-01',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/enrollees/hmo-plan-benefits');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Benefits retrieved successfully',
            'status' => true,
            'plan_name' => 'GUARD'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_get_enrollee_plan_benefits_for_custom_user()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/users/enrollees/hmo-plan-benefits');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Benefits retrieved successfully',
            'status' => true,
            'plan_name' => 'custom'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_fail_to_get_enrollee_plan_benefits_for_custom_user()
    {
        $this->should_create_health_insurance_for_existing_enrollee_with_subscription();

        // delete health insurance object
        $health_insurance = HealthInsurance::first();
        $health_insurance->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/users/enrollees/hmo-plan-benefits');

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
    public function should_fail_to_get_enrollee_plan_benefits_for_legacy_user()
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
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/users/enrollees/hmo-plan-benefits');
        $response->assertStatus(404);
        $response->assertJsonFragment([
            'message' => 'Oops!, something went wrong',
            'status' => false
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
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData());
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
    public function should_forbid_enrollee_from_viewing_admin_resource()
    {
        //this user would be created as guest user by default
        $this->retrieveEnrollee();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/admin/enrollees/' . User::first()->id);
        $response->assertStatus(403);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'You are forbidden from viewing this resource'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_forbid_enrollee_with_active_sub_from_buying_new_hmo_plan()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v2/insurance-calculator/single/male/female');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'Oops!, you can\'t buy a plan at the moment, your subscription is either pending or active.'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_update_enrollee_records_from_legacy_system()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        //invoke authenticate method
        $token    = $this->authenticate();
        DB::table('enrollees')->insert([
            [
                'user_id' => 1,
                'enrollee_id' => 'HTC/17/1/A',
                'phone_number' => '0909090909',
                'email' => 'a.emmanuel2@yahoo.com',
                'plan' => 'guard',
                'company' => 'Wellness',
                'hospital_name' => 'Eko Hospital',
                'name' => 'Jane Steve',
                'is_verified' => true,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        DB::table('subscriptions')->insert([
            [
                'user_id' => 1,
                'plan_name' => 'guard',
                'status' => 'inactive',
                'start_date' => '2021-01-01',
                'end_date' => '2021-04-01',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        $enrollee_code = 'HTC/17/1/A';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $enrollee_code]);
        $response->assertStatus(200);
        $this->assertEquals('active', Subscription::first()->status);
        $this->assertEquals('2021-05-01T00:00:00.000Z', Subscription::first()->start_date);
        $this->assertEquals('2022-04-30T00:00:00.000Z', Subscription::first()->end_date);
        $this->assertEquals('shield', Subscription::first()->plan_name);
        $this->assertEquals('08181856273', Enrollee::first()->phone_number);
        $this->assertEquals('Lobel Hospital', Enrollee::first()->hospital_name);
        $this->assertEquals('HAZON TECH LIMITED', Enrollee::first()->company);
        $this->assertEquals('sotuu@hyprops.com', Enrollee::first()->email);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);
    }

    /**
     * A basic feature test example.
     *@test
     * @return void
     */
    public function should_not_update_custom_enrollee_records_in_database()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        //invoke authenticate method
        $token    = $this->authenticate();
        DB::table('enrollees')->insert([
            [
                'user_id' => 1,
                'enrollee_id' => 'WPA/22/3/A',
                'phone_number' => '0909090909',
                'email' => 'a.emmanuel2@yahoo.com',
                'plan' => 'custom',
                'company' => 'Wellness',
                'hospital_name' => 'Eko Hospital',
                'name' => 'Jane Steve',
                'is_verified' => true,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        DB::table('subscriptions')->insert([
            [
                'user_id' => 1,
                'plan_name' => 'custom',
                'status' => 'active',
                'start_date' => '2021-01-01',
                'end_date' => '2021-04-01',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        $enrollee_code = 'WPA/22/3/A';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $enrollee_code]);
        $response->assertStatus(200);
        $this->assertEquals('custom', Subscription::first()->plan_name);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function should_get_dependents_by_principal_code()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => 'GDM/21/303/A']);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/users/dependents');
        // dd($response);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'dependents retrieved successfully',
            'count' => 2,
        ]);
    }

    /**
     * A basic feature test example.
     * @test
     * @return void
     */
    public function should_not_get_dependents_by_principal_code()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();

        DB::table('enrollees')->insert([
            [
                'user_id' => 1,
                'enrollee_id' => 'GDM/23/1000/A',
                'phone_number' => '0909090909',
                'email' => 'a.emmanuel2@yahoo.com',
                'plan' => 'guard',
                'company' => 'Wellness',
                'hospital_name' => 'Eko Hospital',
                'name' => 'Jane Steve',
                'is_verified' => true,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ],
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/users/dependents');
        $response->assertStatus($response->getStatusCode());
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'something went wrong',
            'count' => 0,
        ]);
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
     * @param void
     * @return void
     *
     */
    public function retrieveEnrollee()
    {
        //disable laravel exceptionhandler
        $this->withoutExceptionHandling();
        //invoke authenticate method
        $token    = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => $this->code]);
        $response->assertStatus(200);
        $this->assertEquals(0, Enrollee::first()->is_verified);
        //assert that message is The enrollee id is valid
        $response->assertJsonFragment([
            'message' => 'Enrollee records retrieved successfully'
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

        //set user's data
        $user = $this->data();
        //post data to registration end-point
        $this->post('/api/v1/register', $user); // second user (normal user)
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

    private function getFakeEnrollee($user_id){
        $faker = Faker::create();
        return [
            'user_id'=>$user_id,
            'enrollee_id'=>Str::random(10),
            'company'=>$faker->company(),
            'email'=>$faker->unique()->safeEmail(),
            'phone_number'=>$faker->phoneNumber(),
            'hospital_name'=>$faker->city,
            'is_verified'=>true,
            'plan'=>$faker->text(),
            'name'=>$faker->text(),
        ];
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

    private function healthInsuranceData(){
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

}
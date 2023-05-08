<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\OtpCode;
use App\Models\ResetCode;
use JWTAuth;
use Carbon\Carbon;

class AuthManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     *@test 
     */
    public function should_confirm_user_mobile_number()
    {
        //invoke the createUser method
        $this->createUser();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //get the first user record in db
        $user = User::first();
        //set user phone_number
        $phone_number = $user->phone_number;
        //attempt to post to the validate end-point
        $response = $this->post('/api/v1/confirm', ['phone_number' => $phone_number]);
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert message is 'Confirmation successful'
        $this->assertEquals('Confirmation successful', $response->json()['message']);
        //assert status true
        $this->assertEquals(true, $response->json()['status']);
        $response->assertStatus(200);
    }

    /**
     *@test 
     */
    public function should_not_confirm_invalid_mobile_number()
    {
        //invoke the createUser method
        $this->createUser();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //get the first user record in db
        $user = User::first();
        //attempt to post to the validate end-point
        $response = $this->post('/api/v1/confirm', ['phone_number' => '09069790920']);
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert message is 'Confirmation failed, user not found!'
        $this->assertEquals('Confirmation failed, user not found!', $response->json()['message']);
        //assert status true
        $this->assertEquals(false, $response->json()['status']);
        $response->assertStatus(404);
    }

    /**
    * @test
    * Test login
    */
    public function should_log_in_user_with_valid_password_and_phone_number()
    {
        //register a new user
        $this->createUser();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //sttempt to post to the validate end-point
        $response = $this->post('/api/v1/login', ['phone_number' => '08181856273', 'password' => '123456']);
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
        $this->assertArrayHasKey('is_verified', $response->json());
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
    public function should_log_in_user_with_valid_password_and_email()
    {
        //register a new user
        $this->createUser();
        //assert a user exists in db
        $this->assertNotNull(User::first());
        //sttempt to post to the validate end-point
        $response = $this->post('/api/v1/login', ['email' => 'john@example.com', 'password' => '123456']);
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
    *@test
    *Test login
    */
    public function should_not_log_in_user_with_invalid_password()
    {
        //register a new user
        $this->createUser();
        //attempt login
        $response = $this->post('/api/v1/login', ['phone_number' => '08181856273', 'password' => 'secret1234!']);
        //assert it was successful and a token was received
        $response->assertStatus(400);
        $this->assertArrayHasKey('error', $response->json());
        $this->assertEquals('Invalid credentials', $response->json()['error']);
    }


    /**
    *@test
    *Test to authenticate user
    */
    public function should_auntheticate_user()
    {
        // get token
        $token    = $this->authenticate();
        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/authenticate');
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert payload key exist
        $this->assertArrayHasKey('payload', $response->json());
        //assert message is 'Authenticated'
        $this->assertEquals('Authenticated', $response->json()['message']);
        //assert status true
        $this->assertEquals(true, $response->json()['status']);
        //assert that status code is 200
        $response->assertStatus(200);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_send_otp_code()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code', ['email' => 'john@example.com']);
        $this->assertEquals(User::where('email', 'john@example.com')->pluck('id')->first(), OtpCode::first()->user_id);
        //assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        //assert status exist
        $this->assertArrayHasKey('status', $response->json());
        $this->assertArrayHasKey('otp_code', $response->json());
        //assert that response is 200 OK
        $response->assertStatus(200);
        // assert that the right message was sent
        $this->assertEquals('OTP code sent successfully!', $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(true, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_send_otp_code_to_enrollee_legacy_email()
    {
        $this->authenticate();
        //insert a record into enrollee table with the auth user id
        DB::table('enrollees')->insert([
            'user_id' => User::first()->id,
            'enrollee_id' => 'GDM/22/1000/A',
            'phone_number' => '08181856273',
            'email' => 'jane@test.com',
            'plan' => 'guard',
            'company' => 'GDM Consults',
            'name' => 'Jane Doe',
            'is_verified' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $response = $this->post('/api/v1/otp-code', ['email' => 'jane@test.com', 'is_enrollee' => true]);
        $this->assertEquals(User::first()->id, OtpCode::first()->user_id);
        $this->assertEquals(User::first()->id, Enrollee::first()->user_id);
        //assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        //assert status exist
        $this->assertArrayHasKey('status', $response->json());
        $this->assertArrayHasKey('otp_code', $response->json());
        //assert that response is 200 OK
        $response->assertStatus(200);
        // assert that the right message was sent
        $this->assertEquals('OTP code sent successfully!', $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(true, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_not_send_otp_code_to_invalid_enrollee_legacy_email()
    {
        $this->authenticate();
        //insert a record into enrollee table with the auth user id
        DB::table('enrollees')->insert([
            'user_id' => User::first()->id,
            'enrollee_id' => 'GDM/22/1000/A',
            'phone_number' => '08181856273',
            'email' => 'jane@test.com',
            'plan' => 'guard',
            'company' => 'GDM Consults',
            'name' => 'Jane Doe',
            'is_verified' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $response = $this->post('/api/v1/otp-code', ['email' => 'john@test.com', 'is_enrollee' => true]);
        $response->assertStatus(404);
        $this->assertEquals('Enrollee not found', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_not_send_otp_code_to_non_existing_enrollee_user()
    {
        $this->authenticate();
        //insert a record into enrollee table with the auth user id
        DB::table('enrollees')->insert([
            'user_id' => 3,
            'enrollee_id' => 'GDM/22/1000/A',
            'phone_number' => '08181856273',
            'email' => 'jane@test.com',
            'plan' => 'guard',
            'company' => 'GDM Consults',
            'name' => 'Jane Doe',
            'is_verified' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $response = $this->post('/api/v1/otp-code', ['email' => 'jane@test.com', 'is_enrollee' => true]);
        $response->assertStatus(404);
        $this->assertEquals('User not found', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_not_send_otp_code_to_invalid_email()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code', ['email' => 'john@example.k']);
        $response->assertStatus(400);
        $this->assertEquals('The email is incorrect', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_not_send_otp_code_to_unknown_user()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code', ['email' => 'test@example.com']);
        $response->assertStatus(404);
        $this->assertEquals('User not found', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's email
    */
    public function should_verify_user_account()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code/verify', ['email' => 'john@example.com', 'status' => true]);
        $this->assertArrayHasKey('message', $response->json());
        $this->assertArrayHasKey('status', $response->json());
        $response->assertStatus(200);
        $this->assertEquals('Verification successful', $response->json()['message']);
        $this->assertEquals(true, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's account
    */
    public function should_not_verify_user_with_invalid_email()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code/verify', ['email' => 'john@example.k', 'status' => true]);
        $response->assertStatus(400);
        $this->assertEquals('Verification failed, the email is incorrect', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's account
    */
    public function should_not_verify_user_with_false_status_value()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code/verify', ['email' => 'john@example.com', 'status' => false]);
        $response->assertStatus(400);
        $this->assertEquals('Verification failed, expected status to be true', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test 
    *Test to verify user's account
    */
    public function should_not_verify_unknown_user_account()
    {
        $this->createUser();
        $response = $this->post('/api/v1/otp-code/verify', ['email' => 'test@example.com', 'status' => true]);
        $response->assertStatus(404);
        $this->assertEquals('Verification failed, user not found', $response->json()['message']);
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test
    *
    */
    public function should_send_forgot_pwd_code()
    {
        $this->createUser();
        $this->forgotPassword();
    }

    /**
    *@test
    *Test to verify user's email
    */
    public function should_verify_reset_pwd_valid_code()
    {
        $this->createUser();
        $this->forgotPassword();
        $response = $this->post('/api/v1/verify-reset-pwd-code', ['reset_code' => ResetCode::first()->reset_code, 'email' => User::first()->email]);
        $this->assertEquals(User::first()->id, ResetCode::first()->user_id);
        // assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        // assert status exist
        $this->assertArrayHasKey('status', $response->json());
        //assert that response is 200 OK
        $response->assertStatus(200);
        // assert that the right message was sent
        $this->assertEquals('Verification successful', $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(true, $response->json()['status']);
    }

    /**
    *@test
    *Test to verify user's email
    */
    public function should_not_verify_reset_pwd_invalid_code()
    {
        $this->createUser();
        $this->forgotPassword();
        $response = $this->post('/api/v1/verify-reset-pwd-code', ['reset_code' => '123456', 'email' => User::first()->email]);
        $this->assertEquals(User::first()->id, ResetCode::first()->user_id);
        //assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        //assert status exist
        $this->assertArrayHasKey('status', $response->json());
        //assert that response is 400
        $response->assertStatus(400);
        //assert that the right message was sent
        $this->assertEquals('Verification failed', $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(false, $response->json()['status']);
    }

    /**
    *@test
    */
    public function should_update_user_password()
    {
        $this->createUser();
        $response = $this->post('/api/v1/update-pwd', 
                               ['email' => User::first()->email,
                                'password' => '1234567'
                               ]);
        //assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        //assert status exist
        $this->assertArrayHasKey('status', $response->json());
        //assert that response is 200
        $response->assertStatus(200);
        //assert that the right message was sent
        $this->assertEquals('Password updated successfully!', $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(true, $response->json()['status']);
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
    private function createUser()
    {
        //disable laravel default error message for the test case
        $this->withoutExceptionHandling();

        // seed values
        $this->seedRole();
        $this->seedAdmin();

        //set user's data
        $user = $this->data();
        //post data to registration end-point
        $this->post('/api/v1/register', $user);      
    }

    /**
    * @param void
    * @return void
    *
    */
    public function forgotPassword()
    {
        $response = $this->post('/api/v1/forgot-pwd', ['email' => User::first()->email]);
        $this->assertEquals(User::first()->id, ResetCode::first()->user_id);
        //assert a message was sent
        $this->assertArrayHasKey('message', $response->json());
        //assert status exist
        $this->assertArrayHasKey('status', $response->json());
        //assert that response is 200 OK
        $response->assertStatus(200);
        //assert that the right message was sent
        $this->assertEquals('Password reset code has been sent to' . ' ' . User::first()->email, $response->json()['message']);
        //assert that the success key-value is true
        $this->assertEquals(true, $response->json()['status']);
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
            'email'      => 'john@example.com',
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

    private function seedAdmin(){
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
}
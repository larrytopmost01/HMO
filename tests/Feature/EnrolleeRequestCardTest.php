<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\EnrolleeRequestCard;
use App\Models\DependentRequest;
use App\Models\Role;
use App\Models\Subscription;
use JWTAuth;

class EnrolleeRequestCardTest extends TestCase
{
    // private $code = "GDM/22/1000/A";
    private $code = "GDM/22/1000/A";
    use RefreshDatabase;

    public function authenticate()
    {
        $this->createUser();
        $token = JWTAuth::fromUser(User::first());
        return $token;
    }

    private function getAuthUser(){
        $this->createUser();
        $authUser = JWTAuth::user();
        $authUser['enrollee'] = ['enrollee_id' => 'GDM/22/1000/A'];
        $token = JWTAuth::fromUser(User::first());
        return ['user' => $authUser, 'token' => $token];
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
    public function authenticateUser()
    {
        $this->createUser();
        $user = User::where('email', 'john@example.com')->first();
        $token = JWTAuth::fromUser($user);
        // $user->is_verified = true;
        // $user->save();
        return $token;
    }

    /**
     *@test
     */
   public function should_register_enrollee_new_card_request(){

    $this->should_verify_existing_enrollee_and_create_subscription();
    $token = JWTAuth::fromUser(User::first());

    $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>FALSE, 'passport_url'=>'http://localhost/passporturl.com']);

    $response->assertStatus(201);
    $this->assertEquals(TRUE, $response->json()['status']);
    $this->assertEquals('card request was successful', $response->json()['message']);
   }

    /**
     *@test
     */
   public function should_register_enrollee_new_card_request_for_subsequent_requests(){

    $this->should_verify_existing_enrollee_and_create_subscription();
    $token = JWTAuth::fromUser(User::first());

    $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

    // requesting for the first time
    $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>FALSE, 'passport_url'=>'http://localhost/passporturl.com']);

    // subsequent request
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>TRUE, 'passport_url'=>'', 'transaction_id'=>'Xsrc123', 'payment_name'=>'card 3', 'payment_amount'=>500, 'payment_type'=>'1234']);

    $response->assertStatus(201);
    $this->assertEquals(TRUE, $response->json()['status']);
    $this->assertEquals('card request was successful', $response->json()['message']);
   }

    /**
     *@test
     */
    public function should_not_register_enrollee_new_card_request(){

        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>TRUE]);

        $response->assertStatus(400);
        $this->assertEquals(FALSE, $response->json()['status']);
        $this->assertEquals('payment details is required for enrollee who has collected card before', $response->json()['message']);
       }

    /**
     *@test
     */
    public function should_not_register_enrollee_new_card_request_without_passport(){

        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>FALSE]);

        $response->assertStatus(400);
        $this->assertEquals(FALSE, $response->json()['status']);
        $this->assertEquals('passport Url is required', $response->json()['message']);
       }


    /**
     *@test
     */
   public function should_not_register_non_enrollee_card_request(){

    $this->createUser();
    $token = JWTAuth::fromUser(User::first());

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
    ])->post('/api/v1/users/enrollees/request-card', ['card_collected'=>FALSE]);

    $response->assertStatus(403);
    $this->assertEquals(FALSE, $response->json()['status']);
    $this->assertEquals('You are not an active enrollee', $response->json()['message']);
   }


   /**
     *@test
    */
    public function should_register_enrollee_paying_for_card_request(){

        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "HIL/17/2/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', array_merge($this->requestCardData(), ['dependent_code' => null]));

        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals('card request was successful', $response->json()['message']);
    }

    /**
     *@test
    */
    public function should_register_enrollee_paying_for_card_request_for_dependent(){

        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "HIL/17/2/A"]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', $this->requestCardData());
        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals(1, EnrolleeRequestCard::count());
        $this->assertEquals(1, DependentRequest::count());
        $this->assertEquals('card request was successful', $response->json()['message']);
       }


    /**
     *@test
     */
    public function should_not_register_enrollee_new_card_request_payment(){
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "HIL/17/2/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', $this->invalidRequestCardPaymentData());

        $response->assertStatus(400);
        $this->assertEquals(FALSE, $response->json()['status']);
        $this->assertEquals('card collected field must be true', $response->json()['message']);
       }

    /**
     *@test
     */
    public function should_not_register_enrollee_paying_for_card_request_with_invalid_data(){

        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "HIL/17/2/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-card', $this->invalidRequestCardData());

        $response->assertStatus(400);
        $this->assertEquals(FALSE, $response->json()['status']);
        $this->assertEquals($this->invalidDataResponseData(), $response->json()['message']);
       }


       private function invalidDataResponseData(){
           return [
            'payment_name'=> 'is a required field',
            'payment_type'=>'is a required field',
            'payment_amount'=> 'is a required field',
            'passport_url'=> 'is a required field'
           ];
       }

    private function requestCardData(){
        return [
            'user_id'=>1,
            'enrollee_id' => 'ojkQaZZkle',
            'card_collected'=>true,
            'transaction_id'=>'123er',
            'payment_name'=>'request card',
            'payment_amount'=>800,
            'passport_url'=>'https://lodf fsdf',
            'payment_type'=>'card 2',
            'dependent_code' => 'GDM/22/1000/B1'
        ];
    }
    private function invalidRequestCardPaymentData(){
        return [
            'user_id'=>1,
            'enrollee_id' => 'ojkQaZZkle',
            'card_collected'=>false,
            'transaction_id'=>'123er',
            'payment_name'=>'request card',
            'payment_amount'=>800,
            'passport_url'=>'https://lodf fsdf',
            'payment_type'=>'card 2'
        ];
    }

    private function invalidRequestCardData(){
        return [
            'user_id'=>1,
            'enrollee_id' => 'ojkQaZZkle',
            'card_collected'=>true,
            'transaction_id'=>'123er',
            'payment_amount'=>800,
            'passport_url'=>'https://lodf fsdf',
            'payment_type'=>'card 2'
        ];
    }

    private function userData(){
        return [
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
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use App\Models\Promo;
use App\Models\OtpCode;
use JWTAuth;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * should insert new user record into storage
     * @return void
     */
    public function should_sign_up_user()
    {
        //set user's data
        $user = $this->data();
        // seed role
        $this->seedRole();
        //post data to registration end-point
        $response = $this->post('/api/v1/register', $user);
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert data key exist
        $this->assertArrayHasKey('data', $response->json());
        //assert message is 'Registration successful'
        $this->assertEquals('Registration successful', $response->json()['message']);
        //assert status true
        $this->assertEquals(true, $response->json()['status']);
        //assert is_verified is false
        $this->assertEquals(0, User::first()->is_verified);
        //assert data was inserted into storage
        $this->assertCount(1, User::all());
        // $this->assertCount(1, Promo::all());
        //assert status code is 201
        $response->assertStatus(201);
        // $data = [
        //     'first_name' => 'John',
        //     'last_name'  => 'Doe',
        //     'email'      => 'a.emmanuel2@yahoo.com',
        //     'password'   => '123456',
        //     'phone_number' => '08181856273',
        // ];
    }

    /**
     * 
     * @test should insert new user record into storage
     * @return void
     */
    public function should_not_sign_up_user_with_invalid_email()
    {
        //disable laravel default error message for the test case
        $this->withoutExceptionHandling();
        //set user's data
        $data = [
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'email'      => 'a.emmanuel2@yahoo',
            'password'   => '123456',
            'phone_number' => '08181856273',
        ];
        //post data to registration end-point
        $response = $this->post('/api/v1/register', $data);
        //assert status key exist
        $this->assertArrayHasKey('status', $response->json());
        //assert message key exist
        $this->assertArrayHasKey('message', $response->json());
        //assert message is 'The email is incorrect'
        $this->assertEquals('The email is incorrect', $response->json()['message']);
        //assert status true
        $this->assertEquals(false, $response->json()['status']);
        //assert data was not inserted into storage
        $this->assertCount(0, User::all());
        //assert status code is 400
        $response->assertStatus(400);
    }
    /**
    * @test
    */
    public function should_require_first_name()
    {
        $response = $this->post('/api/v1/register', array_merge($this->data(), ['first_name' => '']));
        $response->assertJsonFragment([
            'error' => 'validation_error',
            'message' => 'First name is required!'
        ]);
    }


    /**
    * @test
    */
    public function should_require_last_name()
    {
        $response = $this->post('/api/v1/register', array_merge($this->data(), ['last_name' => '']));
        $response->assertJsonFragment([
            'error' => 'validation_error',
            'message' => 'Last name is required!'
        ]);
    }

    /**
    * @test
    */
    public function should_require_email()
    {
        $response = $this->post('/api/v1/register', array_merge($this->data(), ['email' => '']));
        // dd($response->json());
        $response->assertJsonFragment([
            'error' => 'validation_error',
            'message' => 'Email is required!'
        ]);
    }

    /**
    * @test
    */
    public function should_require_phone_number()
    {
        $response = $this->post('/api/v1/register', array_merge($this->data(), ['phone_number' => '']));
        $response->assertJsonFragment([
            'error' => 'validation_error',
            'message' => 'Phone number is required!'
        ]);
    }

    // /**
    //  * @test
    //  */
    // public function should_send_promo_details()
    // {
    //     $this->withoutExceptionHandling();
    //     $this->should_sign_up_user();
    //     $user = User::first();
    //     $token = JWTAuth::fromUser($user);
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $token,
    //     ])->get('/api/v2/promo');
    //     $this->assertArrayHasKey('status', $response->json());
    //     $this->assertArrayHasKey('message', $response->json());
    //     $this->assertArrayHasKey('data', $response->json());
    //     $this->assertEquals('Promo retrieved successfully', $response->json()['message']);
    //     $this->assertEquals(Promo::first()->code, $response->json()['data']['code']);
    //     $this->assertEquals(Promo::first()->discount_percent, $response->json()['data']['discount_percent']);
    // }

    //  /**
    //  * @test
    //  */
    // public function should_create_promo_for_user()
    // {
    //     $this->withoutExceptionHandling();
    //     $this->seedRole();
    //     DB::table('users')->insert([
    //         'role_id' => 1,
    //         'first_name' => 'super',
    //         'last_name' => 'admin',
    //         'email' => 'admin@example.com',
    //         'phone_number' => '08012345678',
    //         'is_blocked' => false,
    //         'is_verified' => true,
    //         'password' => Hash::make('password'),
    //         'created_at' => date('Y/m/d'),
    //         'updated_at' => date('Y/m/d')
    //     ]);
    //     $user = User::first();
    //     $token = JWTAuth::fromUser($user);
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $token,
    //     ])->get('/api/v2/promo');
    //     $this->assertArrayHasKey('status', $response->json());
    //     $this->assertArrayHasKey('message', $response->json());
    //     $this->assertArrayHasKey('data', $response->json());
    //     $this->assertEquals('Promo retrieved successfully', $response->json()['message']);
    //     $this->assertEquals(Promo::first()->code, $response->json()['data']['code']);
    //     $this->assertEquals(Promo::first()->discount_percent, $response->json()['data']['discount_percent']);
    // }

     /**
     * @test
     */
    public function should_not_send_promo_details()
    {
        $this->withoutExceptionHandling();
        $this->should_sign_up_user();
        $user = User::first();
        $promo = Promo::first();
        $promo->is_used = 1;
        $promo->save();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/promo');
        $this->assertArrayHasKey('status', $response->json());
        $this->assertArrayHasKey('message', $response->json());
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals('Promo retrieved successfully', $response->json()['message']);
        $this->assertEquals(null, $response->json()['data']['code']);
        $this->assertEquals(null, $response->json()['data']['discount_percent']);
    }

    /**
    * @test
    */
    public function should_not_register_user_with_an_existing_email_and_phone_number()
    {
        $this->should_sign_up_user();
        $response = $this->post('/api/v1/register', $this->data());
        $response->assertJsonFragment([
            'error' => 'validation_error',
            'message' => 'The email has already been taken.'
        ]);
    }

    /**
    * @param void
    * @return data
    */
    public function data()
    {
        return [
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
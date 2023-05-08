<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;
use Tests\Utils\UserUtil;
use Tests\Utils\DeviceReadingsUtil;
use JWTAuth;

class DeviceReadingsTest extends TestCase
{
    use RefreshDatabase;
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
        $user = $this->userData();
        //post data to registration end-point
        $this->post('/api/v1/register', $user);      
    }

    /**
     *@test 
     */
    public function should_register_new_device_readings()
    {
        $token    = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/device-readings', $this->deviceReadingsData());

        $response->assertStatus(201);
        $this->assertEquals('success', $response->json()['status']);
        $this->assertEquals('device readings recorded successfully', $response->json()['message']);
    }

    /**
     *@test 
     */
    public function should_get_device_readings_for_a_user()
    {
        $token    = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/users/device-readings');

        $response->assertStatus(200);
        $this->assertEquals('device readings retrieved successfully', $response->json()['message']);
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

    private function deviceReadingsData(){
        return [
            'blood_pressure_readings' => ['systolic BP' => '50mmHg', 'diastolicBP' => '20mmHg', 'pulse' => '22bpm'],
            'weight_readings' => '70kg',
            'pulse_readings' => ['Sp02' => '90bpm', 'Heart Rate' => '77bpm', 'pi' => '7.1%'],
            'temperature_readings' => '33.7C',
            'blood_sugar_readings' => ['Glucose' => '5.6mg/dl', 'Cholestrol' => '5.9mg/dl']
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
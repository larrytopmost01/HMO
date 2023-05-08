<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\EnrolleeRequestCode;
use App\Models\DependentRequest;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Role;
use App\Models\Enrollee;
use JWTAuth;
use Tests\TestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class EnrolleeRequestCodeTest extends TestCase
{
    use RefreshDatabase;
    // private $code = "GDM/22/1000/A";
    private $code = "GDM/22/1000/A";
    
    /**
     * A basic feature test example.
     *
     * @return void
     */


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
     *@test
     */
   public function should_register_enrollee_request_code(){
        
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-code', array_merge($this->requestCodeData(), ['dependent_code' => null]));

        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals('code request was successful', $response->json()['message']);
   }

   /**
     *@test
     */
    public function should_register_enrollee_request_code_for_dependent(){
        
        $this->should_verify_existing_enrollee_and_create_subscription();
        $token = JWTAuth::fromUser(User::first());

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            ])->post('/api/v1/users/enrollees/retrieve', ['enrollee_id' => "GDM/22/1000/A"]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-code', $this->requestCodeData());

        $response->assertStatus(201);
        $this->assertEquals(TRUE, $response->json()['status']);
        $this->assertEquals(1, EnrolleeRequestCode::count());
        $this->assertEquals(1, DependentRequest::count());
        $this->assertEquals('code request was successful', $response->json()['message']);
   }

    /**
     *@test
     */
   public function should_not_register_enrollee_request_code(){

        $this->createUser();
        $token = JWTAuth::fromUser(User::first());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/v1/users/enrollees/request-code', array_merge($this->requestCodeData(), ['dependent_code' => null]));

        $response->assertStatus(403);
        $this->assertEquals(FALSE, $response->json()['status']);
        $this->assertEquals('You are not an active enrollee', $response->json()['message']);
   }



    private function requestCodeData(){
        return [
            'user_id'=>1,
            'request_code'   => null,
            'enrollee_id' => 'ojkQaZZkle',
            'hospital_name'  => 'this is the hospital name',
            'request_message'      => 'requesting for a message',
            'dependent_code' => 'GDM/22/1000/B1'
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
}
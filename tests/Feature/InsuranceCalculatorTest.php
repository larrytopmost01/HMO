<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Comment;
use App\Models\Role;
use App\Models\Enrollee;
use App\Models\Subscription;
use DateTime;
use JWTAuth;

class InsuranceCalculatorTest extends TestCase
{
    use RefreshDatabase;
    // private $code = "HIL/17/2/A";
    private $code = "HTC/21/1/A";
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
     *Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_demographics_details_for_married_female_without_spouse()
    {
        $token = $this->authenticateUser();
        DB::table('insurance_demographics')->insert([
            [
                'name' => 'Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Spouse Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Principal Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse currently on medication?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse pregnant?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 20]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        DB::table('insurance_benefits')->insert([
            [
                'name' => 'Ambulance Service',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 2]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
        $spouse_sex = null;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/family/female');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);
        $response_data = $response->json()['data'];
        $this->assertEquals(2, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     *Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_demographics_details_for_female_spouse_sex()
    {
        $token = $this->authenticateUser();
        DB::table('insurance_demographics')->insert([
            [
                'name' => 'Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Spouse Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Principal Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse currently on medication?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse pregnant?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 20]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        DB::table('insurance_benefits')->insert([
            [
                'name' => 'Ambulance Service',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 2]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/insurance-calculator/family/male/female');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $this->assertEquals(5, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     *Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_demographics_details_for_male_spouse_sex()
    {
        $token = $this->authenticateUser();
        DB::table('insurance_demographics')->insert([
            [
                'name' => 'Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Spouse Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Principal Age Range',
                'type' => 'family',
                'value' => json_encode(['18-29' => 4, '30-44' => 7, '45-60' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse currently on medication?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 10]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Is spouse pregnant?',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 20]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        DB::table('insurance_benefits')->insert([
            [
                'name' => 'Ambulance Service',
                'type' => 'family',
                'value' => json_encode(['No' => 0, 'Yes' => 2]),
                'sex' => 'mixed',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/insurance-calculator/family/female/male');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);
        $response_data = $response->json()['data'];
        $this->assertEquals(4, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }



    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_insurance_calculator_details_for_single_male()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('single', 'male');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/single/male');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);
        $response_data = $response->json()['data'];
        $this->assertEquals(1, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_insurance_calculator_details_for_single_female()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('single', 'female');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/single/female');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);
        $response_data = $response->json()['data'];
        $this->assertEquals(1, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_insurance_calculator_details_for_family_male()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('family', 'male');


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/family/male');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $this->assertEquals(1, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_retrieve_insurance_calculator_details_for_family_female()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('family', 'female');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/family/female');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'insurance calculator details retrieved successfully'
        ]);

        $response_data = $response->json()['data'];
        $this->assertEquals(1, count($response_data['insurance_demographics']));
        $this->assertEquals(1, count($response_data['insurance_benefits']));
    }

    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_fail_to_retrieve_insurance_calculator_details_for_wrong_type()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('family', 'female');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/nonsense/female');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be single or family'
        ]);
    }

    /**
     * Insurance Calculator feature test
     *@test
     * @return void
     */
    public function should_fail_to_retrieve_insurance_calculator_details_for_wrong_sex()
    {
        $token = $this->authenticateUser();
        $this->seedInsuranceCalculatorDetails('family', 'female');
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v1/insurance-calculator/family/nonsense');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'sex must be male or female'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_create_health_insurance_for_new_enrollee_without_subscription()
    {
        $user_token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData(true, true, null, null));
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
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData(true, true, null, null));
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
    public function should_fail_to_create_health_insurance_for_unequal_benefits_array()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData(false, true, null, null));

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'benefits fields and values array must be of the same length'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_fail_to_create_health_insurance_for_unequal_demographics_array()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $user_token = JWTAuth::fromUser(User::first());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData(true, false, null, null));

        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'demographics fields and values array must be of the same length'
        ]);
    }

    /**
     * Admin feature test.
     *@test
     * @return void
     */
    public function should_create_health_insurance_for_new_enrolle_without_subscription_with_spouse()
    {
        $user_token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $user_token
        ])->post('/api/v1/insurance-calculator', $this->healthInsuranceData(true, true, 'Shade Daniel', 'female'));

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'health insurance subscription created successfully'
        ]);
    }

    /**
     *@test
     * @return void
     */
    public function should_fail_to_proceed_active_or_pending_enrollee_from_buying_new_hmo_plan()
    {
        $this->should_verify_existing_enrollee_and_create_subscription();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . JWTAuth::fromUser(User::first()),
        ])->get('/api/v1/insurance-calculator/family/male');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status'=>false,
            'message'=>'Oops!, you can\'t buy a plan at the moment, your subscription is either pending or active.',
            'data'=>null
        ]);
    }

    // utility functions
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

    private function healthInsuranceData($equal_benefits_array, $equal_demographics_array, $spouse_name, $spouse_sex)
    {
        return [
            'type' => 'single',
            'sex' => 'male',
            'demographics' => [
                'fields' => $equal_demographics_array === true ? ['Age Range'] : ['Age Range', 'Choice of Hosiptal'],
                'values' => ['18-29']
            ],
            'benefits' => [
                'fields' => $equal_benefits_array === true ? ['Ambulance Service', 'Dependent 1'] : ['Ambulance Service', 'Ice Cream'],
                'values' => $equal_benefits_array === true ? ['Yes', '18-24'] : ['Yes']
            ],
            'transaction_id' => 'test_123456',
            'amount_paid' => 2000,
            'hospital' => 'lagos hospital',
            'spouse_name' => $spouse_name,
            'spouse_sex' => $spouse_sex,
            'has_promo' => true,
        ];
    }

   
}
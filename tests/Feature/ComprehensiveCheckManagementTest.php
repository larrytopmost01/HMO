<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use DateTime;
use JWTAuth;

class ComprehensiveCheckManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_get_comprehensive_check_for_men_basic()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/mb');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(35000, $response->json()['data']['cost']);
        $this->assertEquals(3, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(3, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }

    /**
     * @test
     */
    public function should_get_comprehensive_check_for_men_plus()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/mp');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(40000, $response->json()['data']['cost']);
        $this->assertEquals(4, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(4, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }

    /**
     *@test
     */
    public function should_get_comprehensive_check_for_women_basic()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/wb');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(50000, $response->json()['data']['cost']);
        $this->assertEquals(4, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(4, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }

    /**
     *@test
     */
    public function should_get_comprehensive_check_for_women_plus()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/wp');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(55000, $response->json()['data']['cost']);
        $this->assertEquals(4, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(4, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }

    /**
     *@test
     */
    public function should_get_basic_health_check()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/bc');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(10000, $response->json()['data']['cost']);
        $this->assertEquals(5, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(5, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }
        /**
     *@test
     */
    public function should_get_pre_employment_health_check()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/pe');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'comprehensive checks details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(10000, $response->json()['data']['cost']);
        $this->assertEquals(5, count($response->json()['data']['screenings']['names']));
        $this->assertEquals(5, count($response->json()['data']['screenings']['values']));
        $this->assertEquals(count($response->json()['data']['screenings']['names']), count($response->json()['data']['screenings']['values']));
    }
    /**
     *@test
     */
    public function should_not_retrieve_comprehensive_checks_for_invalid_type()
    {
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/comprehensive/care/up');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be: "mb", "mp", "wb" or "wp", "bc", "pe"'
        ]);
    }

    /**
     *@test
     */
    public function should_create_comprehensive_checks_for_men_basic()
    {
        $type = 'mb';
        $service_name = 'comprehensive';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }


    /**
     *@test
     */
    public function should_create_comprehensive_checks_for_men_plus()
    {
        $type = 'mp';
        $service_name = 'comprehensive';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }

    /**
     *@test
     */
    public function should_create_comprehensive_checks_for_women_basic()
    {
        $type = 'wb';
        $service_name = 'comprehensive';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }

    /**
     *@test
     */
    public function should_create_comprehensive_checks_for_women_plus()
    {
        $type = 'wp';
        $service_name = 'comprehensive';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }
    /**
     *@test
     */
    public function should_create_basic_health_check()
    {
        $type = 'bc';
        $service_name = 'Basic Health Check';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }
    /**
     *@test
     */
    public function should_create_pre_employment_health_check()
    {
        $type = 'pe';
        $service_name = 'Pre-Employment Health Check';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'Appointment created successfully'
        ]);
        $this->assertEquals(1, count(HealthCareService::all()));
        $this->assertEquals(1, count(HealthCareAppointment::all()));
        $this->assertEquals(JWTAuth::user()->id, HealthCareAppointment::first()->user_id);
        $this->assertEquals(JWTAuth::user()->id, HealthCareService::first()->user_id);
    }
    /**
     *@test
     */
    public function should_not_create_comprehensive_checks_for_invalid_service()
    {
        $type = 'up';
        $service_name = 'comprehensive';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type, $service_name));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be: "mb", "mp", "wb" or "wp", "bc", "pe"'
        ]);
    }


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

    private function seedCostCentre()
    {
        DB::table('comprehensive_check_cost_centres')->insert([
            [
                'name' => 'mb',
                'price' => 35000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'mp',
                'price' => 40000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'wb',
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'wp',
                'price' => 55000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'bc',
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'pe',
                'price' => 10000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
    private function seedComprehensiveCheckDetails()
    {
        $this->mixed = ['mb', 'mp', 'wb', 'wp'];
        $this->sub_set = ['bc', 'pe'];
        DB::table('comprehensive_checks')->insert([
            [
                'name' => 'Physical Examination and Basic Checks',
                'value' => json_encode([
                    'Consultation & Physical Examination (Height, Weight & BMI)' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                    'Eye Examination including Visual Acuity, Tonometry, Color Vision and Fundoscopy' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                    'CBC' => $this->mixed,
                    'PCV' => $this->sub_set,
                    'Urinalysis' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Diabetic Screening',
                'value' => json_encode(['FBS' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe']]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Hepatitis Screening',
                'value' => json_encode(['HbsAg' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe']]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Tuberculosis Screening',
                'value' => json_encode(['Mantoux Test' => $this->sub_set]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Retroviral Screening',
                'value' => json_encode(['HIV (I & II)' => $this->sub_set]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Cancer Screening',
                'value' => json_encode(['PSA' => ['mp'], 'Pap Smear' => ['wb', 'wp'], 'Breast Scan' => ['wb'], 'Mammogram' => ['wp']]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function comprehensiveHealthServiceData($type, $service_name)
    {
        return [
            'service_name' => $service_name,
            'services' => $type,
            'transaction_id' => 'test_123456',
            'amount_paid' => 2000,
            'hospital_name' => 'lagos hospital',
            'hospital_location' => 'Ipaja',
            'hospital_address' => '22 Lawande Sodimu, Ipaja Lagos.',
            'doctor_name' => 'Dr. John Doe',
            'appointment_date' => '2021-08-25 8.00 AM',
            'comment' => 'test comment',
        ];
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Promo;
use App\Models\HealthCareService;
use App\Models\HealthCareAppointment;
use DateTime;
use JWTAuth;

class HealthServiceAppointmentManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_fetch_upcoming_appointments()
    {
        $token = $this->authenticateUser();
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days
        $this->appointmentData($upcoming_date);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/history/upcoming');
        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('health service appointments retrieved successfully', $response->json()['message']);
        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

        /**
     * @test
     */
    public function should_fetch_past_appointments()
    {
        $token = $this->authenticateUser();
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) - $current_unix_time); // current date + 2 days
        $this->appointmentData($upcoming_date);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/history/past');
        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('health service appointments retrieved successfully', $response->json()['message']);
        $response_data = $response->json()['data'];
        $data_length = sizeof($response_data['data']);
        $this->assertEquals(1, $data_length);
    }

    /**
     * @test
     */
    public function should_not_fetch_appointments_for_invalid_type()
    {
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/history/bad_query_param');
        $response->assertStatus(400);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals('type must be past or upcoming', $response->json()['message']);
    }

     /**
     * @test
     */
    public function should_get_latest_approved_appointment_optical_example()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) + $current_unix_time); // current date + 2 days
        $this->appointmentData($upcoming_date);
        $this->healthServiceData();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/latest');
        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('latest health service appointment retrieved successfully', $response->json()['message']);
    }
    /**
     * @test
     */
    public function should_get_latest_approved_appointment_comprehensive_example()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'wp';
        $this->seedCostCentre();
        $this->seedComprehensiveCheckDetails();
        //create comprehensive service and appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/comprehensive/care/create', $this->comprehensiveHealthServiceData($type));
        //update appointment status to approved
        $appointment = HealthCareAppointment::first();
        $appointment->status = 'approved';
        $appointment->save();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/latest');
        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('latest health service appointment retrieved successfully', $response->json()['message']);
    }

    /**
     * @test
     */
    public function should_get_latest_approved_appointment_cancer_example()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'csw';
        $service_name = 'cancer';
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        //create comprehensive service and appointment
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/cancer/create', $this->cancerHealthServiceData($type, $service_name));
        //update appointment status to approved
        $appointment = HealthCareAppointment::first();
        $appointment->status = 'approved';
        $appointment->save();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/latest');
        $response->assertStatus(200);
        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('latest health service appointment retrieved successfully', $response->json()['message']);
    }

    /**
     * @test
     */
    public function should_not_get_latest_appointment()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $current_unix_time = strtotime(date('Y-m-d'));
        $upcoming_date = date('Y-m-d', (86400 * 2) - $current_unix_time); // current date + 2 days
        DB::table('health_care_appointments')->insert([
            [
                'user_id' => User::first()->id,
                'service_name' => 'optical',
                'hospital_name' => 'Lagoon Hospital',
                'doctor_name' => 'Mr Charles',
                'appointment_date' => $upcoming_date,
                'comment' => 'test comment',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ]
        ]);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/latest');
        $response->assertStatus(404);
        $this->assertEquals(false, $response->json()['status']);
        $this->assertEquals(1, Promo::count());
        $this->assertEquals('no latest health service appointment found', $response->json()['message']);
    }


    /**
     * @test
     */
    public function should_create_promo_code_via_middleware()
    {
        $this->withoutExceptionHandling();
        $this->seedRole();
        DB::table('users')->insert([
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'phone_number' => '+2347034343434',
                'password' => bcrypt('password'),
                'is_verified' => true,
                'is_blocked' => false,
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ]
        ]);
        $this->assertEquals(0, Promo::count());
        $user = User::where('email', 'john@example.com')->first();
        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/v2/appointments/latest');
        $this->assertEquals(1, Promo::count());
        $this->assertEquals(false, (boolean)Promo::first()->is_used);
        $this->assertEquals(User::first()->id, (boolean)Promo::first()->user_id);
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

    private function appointmentData($appointment_date)
    {
        $appointment_date = $appointment_date === null ? new DateTime('now') : $appointment_date;
        DB::table('health_care_appointments')->insert([
            [
                'user_id' => User::first()->id,
                'service_name' => 'optical',
                'hospital_name' => 'Lagoon Hospital',
                'doctor_name' => 'Mr Charles',
                'appointment_date' => $appointment_date,
                'comment' => 'test comment',
                'status' => 'approved',
                'created_at' => date('Y/m/d'),
                'updated_at' => date('Y/m/d')
            ]
        ]);
    }

    private function healthServiceData()
    {
        DB::table('health_care_services')->insert([
            [
                'user_id' => User::first()->id,
                'services' => json_encode(['Consultation/Examination', 'Scaling and Polishing', 'Occlusal   Xrays (twice)']),
                'service_name' => 'dental',
                'transaction_id' => 'Mr Charles',
                'appointment_id' => HealthCareAppointment::first()->id,
                'amount_paid' => new DateTime('now'),
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
                'name' => 'csw',
                'price' => 50000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'csm',
                'price' => 45000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
    private function seedComprehensiveCheckDetails()
    {
        $this->mixed = ['mb', 'mp', 'wb', 'wp'];
        DB::table('comprehensive_checks')->insert([
            [
                'name' => 'Physical Examination and Basic Checks',
                'value' => json_encode([
                            'Consultation & Physical Examination (Height, Weight & BMI)' => $this->mixed,
                            'Eye Examination (Visual Acuity, Tonometry, Color Vision and Fundoscopy' => $this->mixed,
                            'CBC' => $this->mixed,
                            'Urinalysis' => $this->mixed,
                        ]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Diabetic Screening',
                'value' => json_encode(['FBS' => $this->mixed]),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Hepatitis Screening',
                'value' => json_encode(['HbsAg' => $this->mixed]),
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

    private function comprehensiveHealthServiceData($type)
    {
        return [
            'service_name' => 'comprehensive',
            'services' => $type,
            'transaction_id' => 'test_123456',
            'amount_paid' => 2000,
            'hospital_name' => 'lagos hospital',
            'hospital_location' => 'Ipaja',
            'hospital_address' => '22 Lawande Sodimu, Ipaja Lagos.',
            'doctor_name' => null,
            'appointment_date' => date('Y-m-d'),
            'comment' => null,
        ];
    }
    private function seedCancerScreeningsDetails()
    {
        $this->mixed = 'mixed';
        $this->female = 'female';
        DB::table('cancer_screenings')->insert([
            [
                'name' => 'Alpha-fetoprotein (AFP)',
                'description' => 'Liver, germ cell cell cancer of ovaries or testes',
                'sample' => 'Blood',
                'sex' => $this->mixed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'CA 15-3 (Cancer antigen 15-3)',
                'description' => 'Breast cancer and others, including lung, ovarian',
                'sample' => 'Blood',
                'sex' => $this->mixed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'CA 19-9 (Cancer antigen 19-9)',
                'description' => 'Pancreatic, sometimes bowel and bile ducts',
                'sample' => 'Blood',
                'sex' => $this->mixed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'CA-125 (Cancer antigen 125)',
                'description' => 'Ovarian',
                'sample' => 'Blood',
                'sex' => $this->female,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Carcinoembryonic antigen (CEA)',
                'description' => 'bowel, lung, breast, thyroid, pancreatic, liver, cervix and bladder',
                'sample' => 'Blood',
                'sex' => $this->mixed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'FOB (Fecal Occult Blood)',
                'description' => 'Screens for bleeding from the bowel which can indicate bowel cancers',
                'sample' => 'Stool',
                'sex' => $this->mixed,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    private function cancerHealthServiceData($type, $service_name)
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
            'appointment_date' => date('Y-m-d'),
            'comment' => 'test comment',
        ];
    }
}

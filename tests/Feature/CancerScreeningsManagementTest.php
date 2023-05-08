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

class CancerScreeningsManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_get_cancer_screenings_for_women()
    {
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/cancer/csw');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'cancer screenings details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(50000, $response->json()['data']['cost']);
        $this->assertEquals(6, count($response->json()['data']['screenings']));
    }

     /**
     * @test
     */
    public function should_get_cancer_screenings_for_men()
    {
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/cancer/csm');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'cancer screenings details retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(45000, $response->json()['data']['cost']);
        $this->assertEquals(5, count($response->json()['data']['screenings']));
    }

    /**
     * @test
     */
    public function should_not_get_cancer_screenings_for_invalid_type()
    {
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/cancer/css');
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be: csm or csw'
        ]);
    }

    /**
     *@test
     */
    public function should_create_cancer_screenings_for_men()
    {
        $type = 'csm';
        $service_name = 'cancer';
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/cancer/create', $this->cancerHealthServiceData($type, $service_name));
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
    public function should_create_cancer_screenings_for_women()
    {
        $type = 'csw';
        $service_name = 'cancer';
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/cancer/create', $this->cancerHealthServiceData($type, $service_name));
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
    public function should_not_create_cancer_screenings_for_invalid_type()
    {
        $type = 'css';
        $service_name = 'cancer';
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/cancer/create', $this->cancerHealthServiceData($type, $service_name));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'services must be csm or csw'
        ]);
    }
/**
     *@test
     */
    public function should_not_create_cancer_screenings_service_name()
    {
        $type = 'csw';
        $service_name = 'canser';
        $this->seedCostCentre();
        $this->seedCancerScreeningsDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/cancer/create', $this->cancerHealthServiceData($type, $service_name));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'service_name must be cancer'
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
            'has_promo' => true,
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

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

class DentalOpticalCareManagementTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    public function should_get_dental_primary_care()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/dental-optical/care/dental');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'dental primary care retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(3, count($response->json()['data']['sub_service']));
        $this->assertArrayHasKey('sub_service', $response->json()['data']);
        $this->assertArrayHasKey('dental_optical_primary_care', $response->json()['data']);
    }

    /**
     * @test
     */
    public function should_get_optical_primary_care()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/dental-optical/care/optical');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'optical primary care retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertEquals(3, count($response->json()['data']['sub_service']));
        $this->assertArrayHasKey('sub_service', $response->json()['data']);
        $this->assertArrayHasKey('dental_optical_primary_care', $response->json()['data']);
    }

    /**
     * @test
     */
    public function should_get_other_dental_services_for_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $sub_service = true;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/dental', ['sub_service' => true]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'dental services retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertCount(2, $response->json()['data']['other_services']);
    }

     /**
     * @test
     */
    public function should_get_other_optical_services_for_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $sub_service = true;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/optical', ['sub_service' => true]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'optical services retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertCount(1, $response->json()['data']['other_services']);
    }

     /**
     * @test
     */
    public function should_get_other_dental_services_for_false_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $sub_service = true;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/dental', ['sub_service' => false]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'dental services retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertCount(3, $response->json()['data']['other_services']);
    }

    /**
     * @test
     */
    public function should_get_other_optical_services_for_false_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $sub_service = true;
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/optical', ['sub_service' => false]);
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'status' => true,
            'message' => 'optical services retrieved successfully'
        ]);
        $this->assertArrayHasKey('data', $response->json());
        $this->assertCount(3, $response->json()['data']['other_services']);
    }

    /**
     * @test
     */
    public function should_not_get_dental_primary_care_for_wrong_type()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'bad_query_param';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/dental-optical/care/'.$type);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_optical_primary_care_for_wrong_type()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'bad_query_param';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/v2/dental-optical/care/'.$type);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }
    /**
     * @test
     */
    public function should_not_get_other_dental_services_for_null_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'dental';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/dental', ['sub_service' => null]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'sub_service must be provided'
        ]);
    }


    /**
     * @test
     */
    public function should_not_get_other_optical_services_for_null_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'optical';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/' . $type, ['sub_service' => null]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'sub_service must be provided'
        ]);
    }

    /**
     * @test
     */
    public function should_not_get_other_dental_services_for_wrong_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'dental';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/dental', ['sub_service' => 'string']);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'sub service must be true or false of type boolean'
        ]);
    }

     /**
     * @test
     */
    public function should_not_get_other_optical_services_for_wrong_sub_service()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'optical';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/dental', ['sub_service' => 'string']);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'sub service must be true or false of type boolean'
        ]);
    }

     /**
     * @test
     */
    public function should_not_get_other_dental_services_for_wrong_type()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'bad_query_param';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/'.$type, ['sub_service' => true]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }

    
     /**
     * @test
     */
    public function should_not_get_other_optical_services_for_wrong_type()
    {
        $this->seedDentalOpticalCareDetails();
        $this->withoutExceptionHandling();
        $token = $this->authenticateUser();
        $type = 'bad_query_param';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/'.$type, ['sub_service' => true]);
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }

    /**
     * @test
     */
    public function should_create_dental_care_service()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'dental';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/'.$type, $this->dentalHealthServiceData(true, $type));;
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
     * @test
     */
    public function should_create_optical_care_service()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'optical';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/'.$type, $this->opticalHealthServiceData(true, $type));
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
     * @test
     */
    public function should_not_create_dental_care_service_for_invalid_service_name()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'dental';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/bad_query_param', $this->dentalHealthServiceData(true, $type));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }

    /**
     * @test
     */
    public function should_not_create_optical_care_service_for_invalid_service_name()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'optical';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/bad_query_param', $this->opticalHealthServiceData(true, $type));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'type must be dental or optical'
        ]);
    }


    /**
     * @test
     */
    public function should_not_create_dental_care_service_for_unequal_fields()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'dental';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/' . $type, $this->dentalHealthServiceData(false, $type));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'service fields and values array must be of the same length'
        ]);
    }

    /**
     * @test
     */
    public function should_not_create_optical_care_service_for_unequal_fields()
    {
        $this->withoutExceptionHandling();
        $this->seedDentalOpticalCareDetails();
        $token = $this->authenticateUser();
        $type = 'optical';
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/v2/dental-optical/care/create/' . $type, $this->opticalHealthServiceData(false, $type));
        $response->assertStatus(400);
        $response->assertJsonFragment([
            'status' => false,
            'message' => 'service fields and values array must be of the same length'
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

    private function seedDentalOpticalCareDetails()
    {
        DB::table('dental_optical_cares')->insert([
            [
                'name' => 'Dental Check Up',
                'value' => json_encode(['No' => 0, 'Yes' => 15000, 'Sub-Service' => ['Consultation/Examination', 'Scaling and Polishing', 'Occlusal   Xrays (twice)']]),
                'type' => 'dental',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Peri-Apical',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => 'dental',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Occlusal',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => 'dental',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Biteview',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => 'dental',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            [
                'name' => 'Eye Check',
                'value' => json_encode(['No' => 0, 'Yes' => 20000, 'Sub-Service' => ['Consultation/Examination', 'Refraction', 'Tonometry']]),
                'type' => 'optical',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Tonometry',
                'value' => json_encode(['No' => 0, 'Yes' => 5000]),
                'type' => 'optical',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Refraction',
                'value' => json_encode(['No' => 0, 'Yes' => 2500]),
                'type' => 'optical',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Dilation',
                'value' => json_encode(['No' => 0, 'Yes' => 2000]),
                'type' => 'optical',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    private function dentalHealthServiceData($equal_service_array, $type)
    {
        return [
            'service_name' => $type,
            'services' => [
                'fields' => $equal_service_array === true ? ['Dental Check Up', 'Peri-Apical', 'Biteview'] : ['Peri-Apical', 'Biteview'],
                'values' => $equal_service_array === true ? ['Yes', 'Yes', 'No'] : ['No']
            ],
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

    private function opticalHealthServiceData($equal_service_array, $type)
    {
        return [
            'service_name' => $type,
            'services' => [
                'fields' => $equal_service_array === true ? ['Eye Check', 'Dilation', 'Tonometry'] : ['Refraction', 'Dilation'],
                'values' => $equal_service_array === true ? ['Yes', 'Yes', 'No'] : ['No']
            ],
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

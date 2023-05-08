<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\DataBaseQueryHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueryHandler extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     * @return void
     */
    public function should_return_sub_service()
    {
        $this->seedDentalOpticalCareDetails('dental');
        $queryHandler = new DataBaseQueryHandler();
        $result = $queryHandler->executeQuery('Dental Check Up', 'dental');
        $this->assertEquals(3, count($result));
        $this->assertIsArray($result);
        $this->assertEquals('Consultation/Examination', $result[0]);
        $this->assertEquals('Scaling and Polishing', $result[1]);
        $this->assertEquals('Occlusal   Xrays (twice)', $result[2]);
    }

    /**
     * @test
     * @return void
     */
    public function should_return_a_single_service()
    {
        $this->seedDentalOpticalCareDetails('dental');
        $queryHandler = new DataBaseQueryHandler();
        $result = $queryHandler->executeQuery('Peri-Apical', 'dental');
        $this->assertEquals("Peri-Apical", $result);
        $this->assertIsString($result);
    }

    private function seedDentalOpticalCareDetails($type)
    {
        DB::table('dental_optical_cares')->insert([
            [
                'name' => 'Dental Check Up',
                'value' => json_encode(['No' => 0, 'Yes' => 15000, 'Sub-Service' => ['Consultation/Examination', 'Scaling and Polishing', 'Occlusal   Xrays (twice)']]),
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Peri-Apical',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Occlusal',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Biteview',
                'value' => json_encode(['No' => 0, 'Yes' => 4500]),
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}

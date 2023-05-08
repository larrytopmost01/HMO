<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\Proxy;

class ProxyTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function should_validate_enrollee_id()
    {
        $proxy = new Proxy();
        $code = "HIL/17/2/A";
        $response = $proxy->validateEnrollee($code);
        //assert status code is 200
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_get_enrollee_plan_benefits()
    {
        $proxy = new Proxy();
        $plan = "guard";
        $client_code = 'gdm';
        $response = $proxy->getPlanBenefits($plan, $client_code);
        //assert status code is 200
        $this->assertEquals(200, $response->getStatusCode());
        //assert benefits are returned
        $this->assertNotEmpty($response->getBody()->getContents());
    }

    /**
     * @test
     *
     * @return void
     */
    public function should_get_dependents()
    {
        $proxy = new Proxy();
        $principal_code = "GDM/21/303/A";
        $response = $proxy->getDependents($principal_code);
        //assert status code is 200
        $this->assertEquals(200, $response->getStatusCode());
        //assert dependents are returned
        $this->assertNotEmpty($response->getBody()->getContents());
    }
}

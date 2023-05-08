<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\HttpResponse;

class HttpResponseTest extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function should_return_json_response()
    {
        $status = true;
        $message = 'This is a test';
        $data = ['name' => 'John Doe'];
        $status_code = 200;
        $httpResponse = new HttpResponse($status, $message, $data, $status_code);
        $response = $httpResponse->getJsonResponse();
        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($message, $response->getData()->message);
        $this->assertEquals($status, $response->getData()->status);
    }
}

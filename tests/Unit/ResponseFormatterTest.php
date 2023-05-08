<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Utils\ResponseFormatter;

class ResponseFormatterTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function should_return_correct_success_response()
    {
        $response = ResponseFormatter::successResponse('test ran successfuly', [ 'id' => 1]);
        $responseData = $response['data'];
        $this->assertEquals(true, $response['status']);
        $this->assertEquals('test ran successfuly', $response['message']);
        $this->assertEquals(1, $responseData['id']);

    }

    /**
     * @test
     *
     * @return void
     */
    public function should_return_correct_error_response()
    {
        $response = ResponseFormatter::errorResponse('test failed');

        $this->assertEquals(false, $response['status']);
        $this->assertEquals('test failed', $response['message']);
        $this->assertEquals(null, $response['data']);

    }
}
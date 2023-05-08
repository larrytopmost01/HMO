<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\Message;

class MessageTest extends TestCase
{
    /**
     * @test
     * minimal message
     * @return void
     */
    public function should_return_message()
    {
        $message = new Message('MSG_1');
        $this->assertEquals('Enrollee records retrieved successfully', $message->getMessage());
    }
}

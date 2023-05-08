<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Utils\EmailRegexValidator as Validator;

class EmailRegexValidator extends TestCase
{
    /**
     * @test
     * @return void
     */
    public function should_return_a_boolean()
    {
        $this->assertIsBool(Validator::isEmail('example@test.com'));
    }

    /**
     * @test
     * @return void
     */
    public function should_return_true()
    {
        $this->assertTrue(Validator::isEmail('example@test.com'));
    }

    /**
     * @test
     * @return void
     */
    public function should_return_false()
    {
        $this->assertNotTrue(Validator::isEmail('example@test.k'));
    }
}

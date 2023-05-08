<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\MailerJob;
use App\Models\User;
use Mailgun\Model\Message\SendResponse;

class EmailTest extends TestCase
{
    /**
     * @test
     *
     * @return void
     */
    public function should_send_mail()
    {
        $data = [
            'message' => 'Your password reset code is ' . ' ' . '123456',
            'name' => 'emmanuel',
            'subject' => 'Welcome Aboard'
        ];
        $to = 'example@test.com';
        $mail_gun = new MailerJob($to, $data);
        $response = (array)$mail_gun->handle();
        $message = $response["\x00Mailgun\Model\Message\SendResponse\x00message"];
        $this->assertEquals('Queued. Thank you.', $message);
    }
}
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Mailer;

class MailerJob implements ShouldQueue
{
    public $to;
    public $data;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $data)
    {
        $this->to = $to;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $sendgrid = new Mailer($this->to, $this->data);
        $mail_gun = new Mailer($this->to, $this->data);
        $messageResponse = $mail_gun->deliver();
        return $messageResponse;
    }
}
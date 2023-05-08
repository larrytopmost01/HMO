<?php

namespace App\Utils;

use App\Jobs\MailerJob;
use Carbon\Carbon;

class Jobs
{

  public function mailerJob($to, $data)
  {
    //deffer this task: delegate to Queue worker
    MailerJob::dispatch($to, $data)
    ->delay(Carbon::now()->addSeconds(5));
  }
}
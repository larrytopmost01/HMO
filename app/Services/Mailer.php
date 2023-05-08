<?php

namespace App\Services;

use App\Utils\OtpGenerator as OTP;
use SendGrid\Mail\Mail;
use Mailgun\Mailgun;
use SendGrid;
use Config;

class Mailer
{
  public $from;
  public $to;
  public $subject;
  public $content;
  public $sender;
  public $recipient_name;


  
 /**
  * Create a new mailer instance.
  *
  * @return void
  */
  public function __construct($to, $data)
  {
    //get sendgrid api key
    // $this->sendgrid_api_key = Config::get('keys.sendgrid_api_key');
    $this->mailgun_api_key = Config::get('keys.mailgun_api_key');
    //set subject
    $this->subject = $data['subject'];
    //get recipient name
    $this->recipient_name = $data['name'];
    //get message
    $this->content = $data['message'];
    //get recipient email
    $this->to = $to;
    //set from email
    $this->from = 'no-reply@wellnesshmo.live';
    //set sender
    $this->sender = 'Wellness HealthCare Group';
    //domain
    $this->domain = 'admin.wellnesshmo.live';
  }


   /**
  * Build the message.
  * @param void
  * @return $response
  */
  public function build()
  {
    //create new sendgrid mail client instance
    // $email = new Mail();
    // //set email headers for sendgrid
    // $email->setFrom($this->from, $this->sender);
    // $email->setSubject($this->subject);
    // $email->addTo($this->to, $this->recipient_name);
    // $email->addContent('text/plain', $this->content);
    // return $this->responseExceptionHandler($email);

    $data = [
      'from' => $this->from,
      'to' => $this->recipient_name . ' ' . $this->to,
      'subject' => $this->subject,
      'text' => $this->content
    ];
    //forward data to response exception handler
    return $this->responseExceptionHandler($data);
  }


  /**
  * @param void
  * @return $this
  */
  public function deliver()
  {
    return $this->build();
  }


  /**
  * @param $email
  * @return $response
  */
  public function responseExceptionHandler($data)
  {
      // try {
      //     //instantiate sendgrid mailer client
      //     //pass in your sendgrid api key
      //     $email = new SendGrid($this->sendgrid_api_key);
      //     //attempt to send and await response from sendgrid
      //     $response = $sendgrid->send($email);
      //     return $response;
      // } catch (Exception $e) {
      //     return 'Caught exception: '. $e->getMessage() ."\n";
      // }

      try {
        $mgClient = Mailgun::create($this->mailgun_api_key);
        # Make the call to the client.
        $result = $mgClient->messages()->send($this->domain, $data);
        return $result;
      } catch (Exception $e) {
          return 'Caught exception: '. $e->getMessage() ."\n";
      }
  }
}
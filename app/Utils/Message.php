<?php
namespace app\Utils;
use Illuminate\Http\Response;

class Message{

/*
|--------------------------------------------------------------------------
| MESSAGE REGISTER
|--------------------------------------------------------------------------
|
| Here is where you can register messages for your responses. These
| should be instantiated before the response is sent to the user.
| to import this class into your current file/class use the statement 'use App/Utils/Message';
| then you can use the class like this: 
| $message = new Message('MSG_1');
| $msg = $message->getMessage();
| OR you can also use the class like this: using the getMessage() method otheriwse use the $message variable since it's public
| (new Message('MSG_1'))->getMessage()); 
| (new Message('MSG_1'))->message);
*/
  public $message;
  const MSG_1 = 'Enrollee records retrieved successfully';
  const MSG_2 = 'Sorry, that enrollee id has already been assigned to an existing user';
  const MSG_3 = 'The enrollee id has been mapped to your account, please kindly verify to proceed';
  const MSG_4 = 'That enrollee id does not belongs to you';
  const MSG_5 = 'That enrollee id is invalid';
  const MSG_6 = 'Verification successful';
  const MSG_7 = 'Verification failed';
  const MSG_8 = 'Enrollee has already been verified';
  const MSG_9 = 'Benefits retrieved successfully';
  const MSG_10 = 'Oops!, something went wrong';
  const MSG_11 = 'Enrollees retrieved successfully';
  const MSG_12 = 'Oops!, your subscription has expired, please contact your HR';
  const MSG_13 = 'You are forbidden from viewing this resource';

  public function __construct($message) {
    //We would retrieve the message via the register method, using key value pair
    $this->message = $this->register()[$message];
  }

  public function getMessage() {
    return $this->message;
  }

  public function register() {
    return [
        'MSG_1'   => self::MSG_1,
        'MSG_2'   => self::MSG_2,
        'MSG_3'   => self::MSG_3,
        'MSG_4'   => self::MSG_4,
        'MSG_5'   => self::MSG_5,
        'MSG_6'   => self::MSG_6,
        'MSG_7'   => self::MSG_7,
        'MSG_8'   => self::MSG_8,
        'MSG_9'   => self::MSG_9,
        'MSG_10'  => self::MSG_10,
        'MSG_11'  => self::MSG_11,
        'MSG_12'  => self::MSG_12,
        'MSG_13'  => self::MSG_13
    ];
  }

}




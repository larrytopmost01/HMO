<?php

namespace App\Utils;

use App\Models\User;
use App\Models\OtpCode;
use App\Models\ResetCode;

class ShortCodeGenerator
{
  protected $user;

  public function __construct($user)
  {
    $this->user = $user;
  }

  public function getOtpCode()
  {
    return $this->saveOtpCode(OtpCode::where('user_id', $this->user->id)->first());
  }

  public function getResetCode()
  {
    return $this->saveResetCode(ResetCode::where('user_id', $this->user->id)->first());
  }

  protected function saveOtpCode($code)
  {
    if($code !== null)
    {
      $code->otp_code = $this->generateShortCode();
      $code->save();
    }else{
      $code = new OtpCode();
      $code->otp_code = $this->generateShortCode();
      $this->user->otpcode()->save($code);
    }
    return $code->otp_code;
  }

  protected function saveResetCode($code)
  {
    if($code !== null)
    {
      $code->reset_code = $this->generateShortCode();
      $code->save();
    }else{
      $code = new ResetCode();
      $code->reset_code = $this->generateShortCode();
      $this->user->resetcode()->save($code);
    }
    return $code->reset_code;
  }

  protected function generateShortCode()
  {
    return substr(str_shuffle("0123456789013256794501239786"), 0, 6);
  }
}
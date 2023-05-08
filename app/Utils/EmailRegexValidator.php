<?php
namespace app\Utils;

class EmailRegexValidator
{
  public static function isEmail($email)
  {
    return !preg_match('/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*\.[a-zA-Z]{2,6}$/', $email) ? false : true;
  }
}
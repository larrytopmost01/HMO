<?php

namespace App\Utils;
use App\Models\Promo;

class PromoCodeGenerator
{
  public static function generatePromoCode()
  {
    return self::validatePromoCode(substr(str_shuffle("A01B23C45D6789E01F23G45H67I89J01K23L45M67N89OP01Q23R45S67T89U01V23W45X67Y89Z"), 0, 9));
  }
  protected static function validatePromoCode($code)
  {
    if(Promo::where('code', $code)->first() !== null) return self::generatePromoCode();
    return $code;
  }
}
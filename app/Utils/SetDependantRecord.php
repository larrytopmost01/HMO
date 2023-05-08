<?php
namespace app\Utils;

use Illuminate\Support\Facades\Response;
use App\Utils\Proxy;
use App\Models\Enrollee;

class SetDependantRecord
{
 public function __construct($dependant_code){
  $this->unique_code = $this->getUniqueCode($dependant_code);
  $this->principal_record = null;
  $this->missing_records = [];
 }

 //input: "GDM/15/001/B1"  Output: "GDM/15/001"
 public function getUniqueCode($code)
 {
    if(strtoupper(substr($code, -1)) === 'A'){
      return substr($code, 0, -2);
    }else{
      return substr($code, 0, -3);
    }
 }

 public function getPrincipalCode()
 {
   $proxy = new Proxy();
  //  dd($this->unique_code . '/A');
   $response = $proxy->validateEnrollee($this->unique_code . '/A');
   if($response->getStatusCode() == 200){
     $this->principal_record = json_decode($response->getBody()->getContents(), true);
     $this->missing_records = ['hospital' => $this->principal_record['data']['HCP Name']];
     $principal_code = $this->getUniqueCode($this->principal_record['data']['Code']);
     return $principal_code;
    }else{
      return null;
    }
 }

  public function getDependantMissingRecords()
  {
    return $this->getPrincipalCode() === $this->unique_code ? $this->missing_records["hospital"] : null;
  }
}
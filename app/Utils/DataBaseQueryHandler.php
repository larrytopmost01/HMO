<?php
namespace app\Utils;
use App\Models\DentalOpticalCare;
use App\Models\ComprehensiveCheck;

class DataBaseQueryHandler
{
    public function getDentalOpticalResult($query_string, $type)
    {
      $query_result = DentalOpticalCare::where('name', $query_string)->where('type', $type)->get();
      foreach ($query_result as $item) {
        $item_value = json_decode($item->value);
        foreach ($item_value as $key => $val) {
            if($key === 'Sub-Service'){
                return $val;
            }
        }
      }
      return $query_string;
    }

    public function getComprehensiveCheckResult($type)
    {
      $names = [];
      $values = [];
      $result = [];
      $result_set = ComprehensiveCheck::all();
      foreach ($result_set as $item) {
        $item_value = json_decode($item->value);
        $keys = [];
        foreach ($item_value as $key => $val) {
          if(in_array($type, $val)){
            if(!in_array($item->name, $names)){
              array_push($names, $item->name);
            }
            array_push($keys, $key);
          }
        }
        if(count($keys) > 0){
          array_push($values, $keys);
        }
      }
      $sub_values = [];
      //replace "Eye Examination including Visual Acuity, Tonometry, Color Vision and Fundoscopy" with "Eye Examination including Visual Acuity"
      if(in_array($type, ['bc', 'pe'])){
      $sub_values = str_replace('Eye Examination including Visual Acuity, Tonometry, Color Vision and Fundoscopy', 'Eye Examination including Visual Acuity', $values[0]);
      }
      $values[0] = $sub_values;
      $result['names'] = $names;
      $result['values'] = $values;
      return $result;
    }
}
<?php

namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Utils\ResponseFormatter;
use App\Utils\Proxy;
use Illuminate\Support\Facades\DB;
use JWTAuth;

class DependentController extends Controller
{
   public function getDependents(Proxy $proxy)
   {
      $user = JWTAuth::parseToken()->authenticate();
      $enrollee = $user->enrollee;
      $principal_code = $enrollee->enrollee_id;
      $response = $proxy->getDependents($principal_code);
      if($response->getStatusCode() == Response::HTTP_OK){
        $response = json_decode($response->getBody()->getContents(), true);
        $message = $response['message'];
        $count = $response['count'];
        $dependents = $response['data'];
        $dependents_array = [];
        if($count > 0){
          foreach($dependents as $dependent){
            $dependent_object = [
              'name' => $dependent['Family Name'] . ' ' . $dependent['Name'],
              'code' => $dependent['Code'],
              'sex'  => $dependent['Sex'],
              'age' => $dependent['Age'],
              'dob' => date('jS F Y', strtotime($dependent['DoB'])),
              'relationship' => $dependent['Relationship'],
              'plan' => $dependent['Plan'],
              'status' => $dependent['Status'],
            ];
            array_push($dependents_array, $dependent_object);
          }
        }
          return response()->json([
                'status'     => true,
                'message'    => $message,
                'count'      => $count,
                'data'       => $dependents_array
          ], Response::HTTP_OK);
      }else{
          return response()->json([
              'status'     => false,
              'message'    => 'something went wrong',
              'count'      => 0,
              'data'       => []
        ], $response->getStatusCode());
      }
   }
}
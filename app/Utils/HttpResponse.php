<?php
namespace app\Utils;
use Illuminate\Http\Response;

class HttpResponse {
  /*
|--------------------------------------------------------------------------
| FORMAT HTTP RESPONSE
|--------------------------------------------------------------------------
|
| Here is where you can format your http responses. These
| should be instantiated before the response is sent to the user if you're not using one liner.
| to import this class into your current file/class use the statement 'use App/Utils/HttpResponse';
| then you can use the class like this: 
| $httResponse = new HttpResponse($status, $message, $data, $status_code);
| $response = $httResponse->getJsonResponse();
| OR you can use one liner:
| $response = (new HttpResponse($status, $message, $data, $status_code))->getJsonResponse());
*/
  public $status_code;
  public $message;
  public $data;
  public $status;

  public function __construct($status, $message, $data, $status_code) {
    $this->status_code = $status_code;
    $this->message = $message;
    $this->data = $data;
    $this->status = $status;
  }

  public function getStatusCode() {
    return $this->status_code;
  }

  public function getMessage() {
    return $this->message;
  }

  public function getData() {
    return $this->data;
  }

  public function getStatus() {
    return $this->status;
  }

  public function getJsonResponse() {
    $response = response()->json([
      'status' => self::getStatus(), 
      'message' => self::getMessage(), 
      'data' => self::getData()], 
      self::getStatusCode());
      return $response;
  }

}




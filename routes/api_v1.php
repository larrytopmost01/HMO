<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController as UserControllerV1;
use App\Http\Controllers\Api\V1\AuthController as AuthControllerV1;
use App\Http\Controllers\Api\V1\DeviceReadingsController;
use App\Http\Controllers\Api\V1\EnrolleeController;
use App\Http\Controllers\Api\V1\HmoController;
use App\Http\Controllers\Api\V1\HospitalController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\EnrolleeRequestCodeController;
use App\Http\Controllers\Api\V1\EnrolleeRequestCardController;
use App\Http\Controllers\Api\V1\InsuranceCalculatorController;
use App\Http\Controllers\Api\V1\AppVersionController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/version', [AppVersionController::class, 'getAppVersion']);
Route::post('/register', [UserControllerV1::class, 'registerUser']);
Route::post('/confirm', [AuthControllerV1::class, 'confirmMobileNumber']);
Route::post('/login', [AuthControllerV1::class, 'login']);
Route::post('/admin/login', [AuthControllerV1::class, 'login'])->middleware('admin');
Route::post('/otp-code', [AuthControllerV1::class, 'sendOtpCode']);
Route::post('/otp-code/verify', [AuthControllerV1::class, 'verifyUserAccount']);
Route::post('/forgot-pwd', [AuthControllerV1::class, 'sendResetCode']);
Route::post('/verify-reset-pwd-code', [AuthControllerV1::class, 'verifyResetCode']);
Route::post('/update-pwd', [AuthControllerV1::class, 'updatePassword']);

Route::group(['prefix' => '/users', 'middleware' => ['api']], function () {
  //protected routes for aunthenticated users
  Route::get('/authenticate', [AuthControllerV1::class, 'getAuthenticatedUser']);
  Route::post('/device-readings', [DeviceReadingsController::class, 'recordDeviceReadings']);
  Route::get('/device-readings', [DeviceReadingsController::class, 'viewDeviceReadings']);
  Route::post('/enrollees/retrieve', [EnrolleeController::class, 'getEnrollee'])->middleware('updateEnrolleeRecord');
  Route::post('/enrollees/verify', [EnrolleeController::class, 'verifyEnrollee']);


  //protected routes for subscribed aunthenticated enrollees
  Route::group(['middleware' => ['verifyEnrollee', 'checkStatus'], 'prefix' => '/enrollees'], function () {
    Route::get('/hmo-plan-benefits', [EnrolleeController::class, 'getEnrolleePlanBenefits']);
    Route::post('/request-code',  [EnrolleeRequestCodeController::class, 'storeRequestCode']);
    Route::post('/request-card',  [EnrolleeRequestCardController::class, 'storeCardRequest']);
  });
});

Route::group(['prefix' => '/admin', 'middleware' => ['api', 'admin']], function () {
  Route::get('/enrollees', [AdminController::class, 'getAllEnrollees']);
  Route::get('/enrollees/{id}', [AdminController::class, 'getEnrolleeById']);
  Route::get('/non-enrollees/{id}', [AdminController::class, 'getNonEnrolleeById']);
  Route::get('/enrollees/request-card/{id}',  [AdminController::class, 'getCardRequestById']);
  Route::get('/enrollees/request-code/{id}',  [AdminController::class, 'getRequestCodeById']);
  Route::get('/enrollees/drug-refills/{id}',  [AdminController::class, 'getDrugRefillById']);
  Route::get('/enrollees/hmo/appointments/{id}',  [AdminController::class, 'getHospitalAppointmentById']);
  Route::get('/non-enrollees', [AdminController::class, 'getNonEnrollees']);
  Route::patch('/enrollees/{enrollee_primary_key}', [AdminController::class, 'acceptPendingEnrollee']);
  Route::get('/request-card/{status}',  [AdminController::class, 'getCardRequests']);
  Route::patch('/request-card/{card_request_id}/{status}',  [AdminController::class, 'updatePendingCardRequest']);
  Route::get('/request-code/{status}',  [AdminController::class, 'getCodeRequests']);
  Route::patch('/request-code/{code_request_id}/{status}',  [AdminController::class, 'updatePendingCodeRequest']);
  Route::get('/dashboard',  [AdminController::class, 'dashboard']);
  Route::patch('/drug-refills/{drug_refill_id}/{status}', [AdminController::class, 'approveOrDeclineDrugRefill']);
  Route::get('/drug-refills/{status}', [AdminController::class, 'viewDrugRefills']);
  Route::patch('/hospital-appointments/{hospital_appointment_id}/{status}', [AdminController::class, 'approveOrDeclineHospitalAppointment']);
  Route::get('/hospital-appointments/{status}', [AdminController::class, 'viewHospitalAppointments']);

});

Route::group(['prefix' => '/hmo', 'middleware' => ['api', 'verifyEnrollee', 'checkStatus']], function () {
  Route::post('/appointments', [HmoController::class, 'bookHospitalAppointment']);
  Route::patch('/appointments/{hospital_appointment_id}', [HmoController::class, 'rescheduleHospitalAppointment']);
  Route::delete('/appointments/{hospital_appointment_id}', [HmoController::class, 'cancelHospitalAppointment']);
  Route::get('/appointments/{type}', [HmoController::class, 'viewHospitalAppointments']);
  Route::post('/drug-refills', [HmoController::class, 'requestForDrugRefill']);
  Route::get('/tele-med', [HmoController::class, 'teleMedicine']);
  Route::post('/comments', [HmoController::class,'comment']);

});

Route::group(['prefix' => '/insurance-calculator', 'middleware' => ['api']], function () {
  Route::post('/', [InsuranceCalculatorController::class, 'createHealthInsurance']);
});

Route::get('/insurance-calculator/{type}/{sex}', [InsuranceCalculatorController::class, 'getInsuranceCalculatorDetails'])
    ->middleware('api', 'validateSubscription');

Route::group(['prefix' => '/hospital'], function () {
  Route::get('/locations', [HospitalController::class, 'getAllLocationsForNonEnrollees']);
  Route::get('/locations/{location}', [HospitalController::class, 'getHospitalsByLocationForNonEnrollees']);
});

Route::group(['prefix' => '/hospital', 'middleware' => ['api']], function () {
  Route::group(['prefix' => '/enrollees', 'middleware' => ['verifyEnrollee']], function () {
    Route::get('/locations', [HospitalController::class, 'getAllLocationsForEnrollees']);
    Route::get('/locations/{location}', [HospitalController::class, 'getHospitalsByLocationForEnrollees']);
  });
});
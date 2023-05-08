<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\InsuranceCalculatorController;
use App\Http\Controllers\Api\V2\DentalOpticalCareController;
use App\Http\Controllers\Api\V2\ComprehensiveCheckController;
use App\Http\Controllers\Api\V2\HealthCentreLocationController;
use App\Http\Controllers\Api\V2\WellnessPlusAdminController;
use App\Http\Controllers\Api\V2\AppointmentHistoryController;
use App\Http\Controllers\Api\V2\CancerScreeningController;
use App\Http\Controllers\Api\V2\HospitalController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V2\DependentController;


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

Route::get('/promo', [UserController::class, 'getPromo'])->middleware('api');
Route::get('/insurance-calculator/{type}/{sex}/{spouse_sex}', 
          [InsuranceCalculatorController::class, 'setSpouseDemographicDetails'])
          ->middleware('validateSubscription');
Route::group(['prefix' => '/dental-optical/care', 'middleware' => ['api', 'updatePromo']], function () {
  Route::get('/{type}', [DentalOpticalCareController::class, 'getDentalOpticalPrimaryCare']);
  Route::post('/{type}', [DentalOpticalCareController::class, 'getOtherDentalOpticalServices']);
  Route::post('/create/{type}', [DentalOpticalCareController::class, 'createDentalOpticalCare']);
});

Route::group(['prefix' => '/comprehensive/care', 'middleware' => ['api', 'updatePromo']], function () {
  Route::get('/{type}', [ComprehensiveCheckController::class, 'getComprehensiveCheck']);
  Route::post('/create', [ComprehensiveCheckController::class, 'createComprehensiveHealthCheck']);
});

Route::group(['prefix' => '/cancer', 'middleware' => ['api', 'updatePromo']], function () {
  Route::get('/{type}', [CancerScreeningController::class, 'getCancerScreenings']);
  Route::post('/create', [CancerScreeningController::class, 'createCancerScreenings']);
});

Route::group(['prefix' => '/health-centre/locations', 'middleware' => ['api']], function () {
  Route::get('/{type}', [HealthCentreLocationController::class, 'getHealthCentreLocations']);
  Route::get('/{type}/{location}', [HealthCentreLocationController::class, 'getHealthCentresByLocation']);
});

Route::group(['prefix' => '/appointments', 'middleware' => ['api']], function () {
  Route::get('/history/{type}', [AppointmentHistoryController::class, 'viewHealthServiceAppointments']);
  Route::get('/latest', [AppointmentHistoryController::class, 'viewLatestAppointment']);
});

Route::group(['prefix' => '/users', 'middleware' => ['api']], function () {
  Route::get('/dependents', [DependentController::class, 'getDependents']);
});

Route::group(['prefix' => '/admin', 'middleware' => ['api', 'admin']], function () {
  Route::get('/users', [WellnessPlusAdminController::class, 'getUsers']);
  Route::get('/users/{id}', [WellnessPlusAdminController::class, 'getUserById']);  
  Route::get('/appointments/{type}/{status}', [WellnessPlusAdminController::class, 'getAppointments']);
  Route::get('/appointment-details/{id}', [WellnessPlusAdminController::class, 'getAppointmentDetails']);
  Route::patch('/appointment/request-code/{appointment_id}/{status}',  [WellnessPlusAdminController::class, 'updateAppointmentStatus']);
  Route::group(['prefix' => '/settings'], function(){
    Route::get('/hospitals', [WellnessPlusAdminController::class, 'getHospitals']);
    Route::get('/hospitals/{id}', [WellnessPlusAdminController::class, 'getHospitalById']);
  });
  Route::get('/dashboard',  [WellnessPlusAdminController::class, 'dashboard']);
  Route::get('/hospital-levels', [WellnessPlusAdminController::class, 'getHospitalLevels']);
  Route::get('/hospitals', [WellnessPlusAdminController::class, 'getHospitalsForAdmin']);
  Route::post('/hospitals', [WellnessPlusAdminController::class, 'storeHospital']);
  Route::get('/hospitals/{id}', [WellnessPlusAdminController::class, 'showHospitalById']);
  Route::patch('/hospitals/{id}', [WellnessPlusAdminController::class, 'updateHospitalById']);
  Route::delete('/hospitals/{id}', [WellnessPlusAdminController::class, 'deleteHospitalById']);
  Route::get('/services', [WellnessPlusAdminController::class, 'getServices']);
  Route::post('/services', [WellnessPlusAdminController::class, 'storeService']);
  Route::get('/services/{id}', [WellnessPlusAdminController::class, 'showServiceById']);
  Route::patch('/services/{id}', [WellnessPlusAdminController::class, 'updateServiceById']);
  Route::delete('/services/{id}', [WellnessPlusAdminController::class, 'deleteServiceById']);
  Route::get('/health-service-providers', [WellnessPlusAdminController::class, 'getHealthServiceProviders']);
  Route::post('/health-service-providers', [WellnessPlusAdminController::class, 'storeHealthServiceProvider']);
  Route::get('/health-service-providers/{id}', [WellnessPlusAdminController::class, 'showHealthServiceProviderById']);
  Route::patch('/health-service-providers/{id}', [WellnessPlusAdminController::class, 'updateHealthServiceProviderById']);
  Route::delete('/health-service-providers/{id}', [WellnessPlusAdminController::class, 'deleteServiceProviderById']);
  Route::get('/comment/all', [AdminController::class,'view_comment']);

});
<?php
namespace App\Http\Controllers\Api\V2;

use Illuminate\Http\Request;
use Response;
use App\Utils\ResponseFormatter;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\V1\InsuranceCalculatorController as InsuranceCalculatorControllerV1;


class InsuranceCalculatorController extends Controller
{
    public function setSpouseDemographicDetails(InsuranceCalculatorControllerV1 $insurance_calculator, $type, $sex, $spouse_sex)
    {
        if (!in_array($spouse_sex, ['male', 'female'])) {
            return response()->json(ResponseFormatter::errorResponse('spouse sex must be male or female'), 400);
        }
        return $insurance_calculator->getInsuranceCalculatorDetails($type, $sex, $spouse_sex);
    }
}
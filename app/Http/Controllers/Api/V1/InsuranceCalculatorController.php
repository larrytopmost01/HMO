<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\SubscriptionHistory;
use App\Models\HealthInsurance;
use App\Models\User;
use App\Models\Enrollee;
use App\Models\Subscription;
use App\Utils\ResponseFormatter;
use App\Http\Requests\V1\HealthInsuranceRequest;
use Illuminate\Support\Facades\DB;
use App\Utils\Jobs;
use JWTAuth;

class InsuranceCalculatorController extends Controller
{
    public function getInsuranceCalculatorDetails($type, $sex, $spouse_sex = null)
    {
        if (!in_array($type, ['single', 'family'])) {
            return response()->json(ResponseFormatter::errorResponse('type must be single or family'), 400);
        }
        if (!in_array($sex, ['male', 'female'])) {
            return response()->json(ResponseFormatter::errorResponse('sex must be male or female'), 400);
        }

        if($spouse_sex === null){
            $insurance_demographics = DB::table('insurance_demographics')
            ->select(['name', 'type', 'sex', 'value'])
            ->where(function ($query) use (&$type) {
                $query->where('type', '=', $type)
                    ->orWhere('type', '=', 'mixed');
            })
            ->where(function ($query) use (&$sex) {
                $query->where('sex', '=', $sex)
                    ->orWhere('sex', '=', 'mixed');
            })
            ->where('name', 'NOT LIKE', '%' . 'spouse' . '%')
            ->where('name', 'NOT LIKE', '%' . 'Spouse' . '%')
            ->get();
        }elseif($spouse_sex === 'male'){
            $insurance_demographics = DB::table('insurance_demographics')
            ->select(['name', 'type', 'sex', 'value'])
            ->where(function ($query) use (&$type) {
                $query->where('type', '=', $type)
                    ->orWhere('type', '=', 'mixed');
            })
            ->where(function ($query) use (&$sex) {
                $query->where('sex', '=', $sex)
                    ->orWhere('sex', '=', 'mixed');
            })
            ->where('name', '!=', 'Is spouse pregnant?')
            ->get();
        }else{
            $insurance_demographics = DB::table('insurance_demographics')
            ->select(['name', 'type', 'sex', 'value'])
            ->where(function ($query) use (&$type) {
                $query->where('type', '=', $type)
                    ->orWhere('type', '=', 'mixed');
            })
            ->where(function ($query) use (&$sex) {
                $query->where('sex', '=', $sex)
                    ->orWhere('sex', '=', 'mixed');
            })
            ->get();
        }
        $insurance_benefits = DB::table('insurance_benefits')
            ->select(['name', 'type', 'sex', 'value'])
            ->where(function ($query) use (&$type) {
                $query->where('type', '=', $type)
                    ->orWhere('type', '=', 'mixed');
            })
            ->where(function ($query) use (&$sex) {
                $query->where('sex', '=', $sex)
                    ->orWhere('sex', '=', 'mixed');
            })
            ->get();
        // decode serialized json
        if (count($insurance_benefits) > 0 && count($insurance_demographics) > 0) {
            foreach ($insurance_benefits as $item) {
                // $item->value = json_decode($item->value);
                $item_value = json_decode($item->value);
                $names = [];
                $points = [];
                foreach ($item_value as $key => $val) {
                    array_push($names, $key);
                    array_push($points, $val);
                }

                $item->value = [
                    'names' => $names,
                    'points' => $points
                ];
            }
            foreach ($insurance_demographics as $item) {
                // $item->value = json_decode($item->value);
                $item_value = json_decode($item->value);
                $names = [];
                $points = [];
                foreach ($item_value as $key => $val) {
                    array_push($names, $key);
                    array_push($points, $val);
                }
                $item->value = [
                    'names' => $names,
                    'points' => $points
                ];
            }
        }
        
        $final_data = [
            'insurance_demographics' => $insurance_demographics,
            'insurance_benefits' => $insurance_benefits
        ];
        return response()->json(ResponseFormatter::successResponse('insurance calculator details retrieved successfully', $final_data), 200);
    }

    public function createHealthInsurance(HealthInsuranceRequest $request)
    {
        $validatedData = $request->validated();
        $user_id = JWTAuth::user()->id;
        $email_message_suffix = 'active';

        // check that benefits and demographics field have the same length of array
        // for their field & values array
        $benefits_input = $validatedData['benefits'];
        $demographics_input = $validatedData['demographics'];

        if (count($benefits_input['fields']) !== count($benefits_input['values'])) {
            return response()->json(ResponseFormatter::errorResponse('benefits fields and values array must be of the same length'), 400);
        }

        if (count($demographics_input['fields']) !== count($demographics_input['values'])) {
            return response()->json(ResponseFormatter::errorResponse('demographics fields and values array must be of the same length'), 400);
        }

        // calculate end date
        $current_unix_time = strtotime(date('Y-m-d'));
        $new_end_date = date('Y-m-d', (86400 * 364) + $current_unix_time);

        $possible_user = User::where('id', $user_id)->first();
        $possible_enrollee = Enrollee::where('user_id', $user_id)->first();

        /**
         * if possible_enrollee is null, it means the user is a pending enrollee waiting to be approved by the admin
         * which means the subscription_history start_date & end_date would also be updated once the enrollee is approved
         * 
         * else, it means the user is an existing enrollee
         */
        $subscription_history_start_date = $possible_enrollee === null  ? null : date('Y-m-d');
        $subscription_history_end_date = $possible_enrollee === null  ? null : $new_end_date;

        // 1. handle edge cases for new & existing enrollee
        if ($possible_enrollee === null) {
            // for brand new users without an enrollee_id
            $new_enrollee = Enrollee::create([
                'enrollee_id' => '',
                'user_id' => $user_id,
                'hospital_name' => $validatedData['hospital'],
                'company' => 'private',
                'phone_number' => $possible_user->phone_number,
                'email' => $possible_user->email,
                'plan' => 'custom',
                'name' => $possible_user->first_name . ' ' . $possible_user->last_name,
                'is_verified' => true
            ]);
            $new_enrollee->save();
        } else {
            // for existing users with an enrollee_id
            $possible_enrollee->plan = 'custom';
            $possible_enrollee->hospital_name = $validatedData['hospital'];
            $possible_enrollee->company = 'private';

            $possible_enrollee->save();
        }

        // 2. handle edge cases for new and existing subscription
        $possible_subscription = Subscription::where('user_id', $user_id)->first();
        if ($possible_subscription === null) {
            // for brand new users without an enrollee_id
            $new_subscription = Subscription::create([
                'user_id' => $user_id,
                'plan_name' => 'custom',
                'status' => 'pending',
                'start_date' => null,
                'end_date' => null
            ]);

            $new_subscription->save();
            $email_message_suffix = 'pending. You will receive an email once your subscription have been confirmed by the Admin.';
        } else {
            // for existing users with an enrollee_id
            $possible_subscription->status = 'active';
            $possible_subscription->plan_name = 'custom';
            $possible_subscription->start_date = date('Y-m-d');
            $possible_subscription->end_date = $new_end_date;

            $possible_subscription->save();
        }

        // 3. handle edge cases for health insurance and subscription history
        // flesh out details for benefits & demographics
        $benefits_input_fields = $benefits_input['fields'];
        $benefits_input_values = $benefits_input['values'];
        $demographics_input_fields = $demographics_input['fields'];
        $demographics_input_values = $demographics_input['values'];
        $final_benefits = [];
        $final_demographics = [];
        $possible_dependents = [];
        for ($index = 0; $index < count($benefits_input_fields); $index++) {
            $benefit_key = $benefits_input_fields[$index];
            $benefit_value = $benefits_input_values[$index];

            // account for possible dependents
            if ((substr($benefit_key, 0, 9) === 'Dependent')) {
                $possible_dependents[$benefit_key] = $benefit_value;
                continue;
            }

            // compute the associative array for benefits
            if ($benefit_value !== 'No') {
                $final_benefits[$benefit_key] = $benefit_value;
            }
        }

        for ($index = 0; $index < count($demographics_input_fields); $index++) {
            $demographics_key = $demographics_input_fields[$index];
            $demographics_value = $demographics_input_values[$index];

            // compute the associative array for demographics
            $final_demographics[$demographics_key] = $demographics_value;
        }

        if (count($possible_dependents) > 0) {
            $final_demographics = array_merge($final_demographics, $possible_dependents);
        }

        if($request->spouse_name !== null || $request->spouse_name !== ''){
            $final_demographics['Spouse Name'] = $request->spouse_name;
        }

        if($request->spouse_sex !== null || $request->spouse_sex !== ''){
            $final_demographics['Spouse Sex'] = $request->spouse_sex;
        }


        // save details
        $health_insurance = HealthInsurance::create([
            'user_id' => $user_id,
            'type' => $validatedData['type'],
            'sex' => $validatedData['sex'],
            'benefits' => json_encode($final_benefits),
            'demographics' => json_encode($final_demographics)
        ]);
        $health_insurance->save();
        $subscription_history = SubscriptionHistory::create([
            'health_insurance_id' => $health_insurance->id,
            'start_date' => $subscription_history_start_date,
            'end_date' => $subscription_history_end_date,
            'transaction_id' => $validatedData['transaction_id'],
            'amount_paid' => $validatedData['amount_paid'],
            'hospital' => $validatedData['hospital']

        ]);
        $subscription_history->save();

        // send confirmation email
        $user = JWTAuth::user();
        $to = $user->email;
        $data = [
            'name' => $user->first_name . ' ' . $user->last_name,
            'subject' => 'Health Insurance Subscription',
            'message' => 'Thank you for subscribing to Wellness HMO. Your subscription is currently ' . $email_message_suffix .  $health_insurance->benefits . $health_insurance->demographics
        ];
        $jobs = new Jobs();
        $jobs->mailerJob($to, $data);
        // return response
        return response()->json(ResponseFormatter::successResponse('health insurance subscription created successfully'), 201);
    }
}
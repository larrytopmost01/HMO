<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprehensiveCheckSeeder extends Seeder
{
    /*
    |--------------------------------------------------------------------------
    | HEALTH CHECK SERVICES CODE NAME
    |--------------------------------------------------------------------------
    | mb implies men basic
    | mp implies men plus
    | wb implies women basic
    | wp implies women plus
    | bc implies basic check
    | pe implies pre-employment
    | Note that mb, mp. wb, wp falls under comprehensive health check, while bc and pe are sub-set of comprehensive health check
    */
    private $mixed = ['mb', 'mp', 'wb', 'wp'];
    private $sub_set = ['bc', 'pe'];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('comprehensive_checks')->insert([
            [
                'name' => 'Physical Examination and Basic Checks',
                'value' => json_encode([
                            'Consultation & Physical Examination (Height, Weight & BMI)' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                            'Eye Examination including Visual Acuity, Tonometry, Color Vision and Fundoscopy' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                            'PCV' => $this->sub_set,
                            'CBC' => $this->mixed,
                            'Urinalysis' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe'],
                        ])
            ],
            [
                'name' => 'Diabetic Screening',
                'value' => json_encode(['FBS' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe']])
            ],
            [
                'name' => 'Hepatitis Screening',
                'value' => json_encode(['HbsAg' => ['mb', 'mp', 'wb', 'wp', 'bc', 'pe']]),
            ],
            [
                'name' => 'Lipid Screening',
                'value' => json_encode(['Triglyceride' => $this->mixed, 'Cholesterol' => $this->mixed])
            ],
            [
                'name' => 'Liver Screening',
                'value' => json_encode(['GGT' => $this->mixed, 'ALT' => $this->mixed, 'AST' => $this->mixed])
            ],
            [
                'name' => 'Kidney Screening',
                'value' => json_encode(['Urea' => $this->mixed, 'Creatinine' => $this->mixed])
            ],
            [
                'name' => 'Heart Checks',
                'value' => json_encode(['Chest Xray' => $this->mixed, 'ECG' => $this->mixed]),
            ],
            [
                'name' => 'Organs Imaging',
                'value' => json_encode(['USS Abdomen' => $this->mixed])
            ],
            [
                'name' => 'Cancer Screening',
                'value' => json_encode(['PSA' => ['mp'], 'Pap Smear' => ['wb', 'wp'], 'Breast Scan' => ['wb'], 'Mammogram' => ['wp']])
            ],
            [
                'name' => 'Tuberculosis Screening',
                'value' => json_encode(['Mantoux Test' => $this->sub_set])
            ],
            [
                'name' => 'Retroviral Screening',
                'value' => json_encode(['HIV (I & II)' => $this->sub_set])
            ],
        ]);
    }
}

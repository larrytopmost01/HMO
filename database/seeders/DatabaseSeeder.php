<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,  
            AdminSeeder::class,
            InsuranceDemographicsSeeder::class,
            InsuranceBenefitsSeeder::class,
            HospitalLevelSeeder::class,
            // EnrolleeSeeder::class,
            // UserSeeder::class,
            // SubscriptionSeeder::class,
            // CardRequestSeeder::class,
            // CodeRequestSeeder::class,
            // DrugRefillSeeder::class,
            // HospitalAppointmentSeeder::class,
            HospitalSeeder::class,
            ServiceSeeder::class,
            HealthServiceProvidersSeeder::class,
            DentalCareSeeder::class,
            // DentalAppointmentsSeeder::class,
            // OpticalAppointmentsSeeder::class,
            // HealthCareServicesSeeder::class,
            OpticalCareSeeder::class,
            CostCentreSeeder::class,
            ComprehensiveCheckSeeder::class,
            CancerScreeningSeeder::class,
        ]);
    }
}
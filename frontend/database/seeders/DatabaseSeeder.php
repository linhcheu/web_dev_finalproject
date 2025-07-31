<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            HospitalAdminSeeder::class,
            HospitalSeeder::class,
            RegularUserSeeder::class,
            AdditionalUsersSeeder::class,
            AppointmentSeeder::class,
            BadFeedbackSeeder::class,
            PositiveFeedbackSeeder::class, // Added positive feedback seeder
        ]);
    }
}
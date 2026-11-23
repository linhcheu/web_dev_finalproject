<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            HospitalAdminSeeder::class,
            HospitalSeeder::class,
            RegularUserSeeder::class,
            AdditionalUsersSeeder::class,
            AppointmentSeeder::class,
            BadFeedbackSeeder::class,
            PositiveFeedbackSeeder::class,
        ]);
    }
}

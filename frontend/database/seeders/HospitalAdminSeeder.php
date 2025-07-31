<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class HospitalAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitalAdmins = [
            [
                'first_name' => 'Calmette Hospital',
                'last_name' => 'Admin',
                'email' => 'calmete@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Khmer-Soviet Hospital',
                'last_name' => 'Admin',
                'email' => 'russiakh@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Royal Hospital',
                'last_name' => 'Admin',
                'email' => 'royalpp@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sen Sok Hospital',
                'last_name' => 'Admin',
                'email' => 'sensokiu@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Raffles Hospital',
                'last_name' => 'Admin',
                'email' => 'raffles@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sunrise Hospital',
                'last_name' => 'Admin',
                'email' => 'sunrise@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Central Hospital',
                'last_name' => 'Admin',
                'email' => 'central@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Naga Clinic',
                'last_name' => 'Admin',
                'email' => 'nagaclinic@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sihanouk Hospital',
                'last_name' => 'Admin',
                'email' => 'sihanoukcenter@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Procare Clinic',
                'last_name' => 'Admin',
                'email' => 'procare@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Tropical Clinic',
                'last_name' => 'Admin',
                'email' => 'tropical@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Khema Clinic',
                'last_name' => 'Admin',
                'email' => 'khema@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'SOS Medical Center',
                'last_name' => 'Admin',
                'email' => 'sosmedical@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Pacific Hospital',
                'last_name' => 'Admin',
                'email' => 'pacificpp@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'National Pediatric Hospital',
                'last_name' => 'Admin',
                'email' => 'nationalpad@gmail.com',
                'password' => '11111111',
            ],
        ];

        foreach ($hospitalAdmins as $adminData) {
            // Check if user already exists
            $existingUser = User::where('email', $adminData['email'])->first();
            
            if (!$existingUser) {
                User::create([
                    'first_name' => $adminData['first_name'],
                    'last_name' => $adminData['last_name'],
                    'email' => $adminData['email'],
                    'password_hash' => Hash::make($adminData['password']),
                    'role' => 'hospital_admin',
                    'is_active' => true,
                    'phone' => '085' . rand(100000, 999999), // Random phone number
                ]);
            }
        }

        $this->command->info('Hospital admin accounts created successfully!');
    }
}

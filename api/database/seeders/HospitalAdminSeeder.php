<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\frontendModels\User;

class HospitalAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hospitalAdmins = [
            [
                'first_name' => 'Calmette',
                'last_name' => 'Hospital',
                'email' => 'calmete@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Khmer-Soviet',
                'last_name' => 'Hospital',
                'email' => 'russiakh@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Royal',
                'last_name' => 'Hospital',
                'email' => 'royalpp@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sen Sok',
                'last_name' => 'Hospital',
                'email' => 'sensokiu@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Raffles',
                'last_name' => 'Hospital',
                'email' => 'raffles@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sunrise',
                'last_name' => 'Hospital',
                'email' => 'sunrise@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Central',
                'last_name' => 'Hospital',
                'email' => 'central@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Naga',
                'last_name' => 'Clinic',
                'email' => 'nagaclinic@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Sihanouk',
                'last_name' => 'Hospital',
                'email' => 'sihanoukcenter@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Procare',
                'last_name' => 'Clinic',
                'email' => 'procare@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Tropical',
                'last_name' => 'Clinic',
                'email' => 'tropical@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Khema',
                'last_name' => 'Clinic',
                'email' => 'khema@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'SOS',
                'last_name' => 'Medical',
                'email' => 'sosmedical@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'Pacific',
                'last_name' => 'Hospital',
                'email' => 'pacificpp@gmail.com',
                'password' => '11111111',
            ],
            [
                'first_name' => 'National',
                'last_name' => 'Pediatric',
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

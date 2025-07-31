<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegularUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['first_name' => 'Chealinh', 'last_name' => 'Chan'],
            ['first_name' => 'Sodano', 'last_name' => 'Ean'],
            ['first_name' => 'Panharoth', 'last_name' => 'Ear'],
            ['first_name' => 'Chanrithy', 'last_name' => 'Em'],
            ['first_name' => 'Navy', 'last_name' => 'Him'],
            ['first_name' => 'Sothearith', 'last_name' => 'Horn'],
            ['first_name' => 'Sonita', 'last_name' => 'Ieng'],
            ['first_name' => 'Honglee', 'last_name' => 'Kin'],
            ['first_name' => 'Tisamnop', 'last_name' => 'Kong'],
            ['first_name' => 'Bunnarong', 'last_name' => 'Kreang'],
            ['first_name' => 'Sokseyha', 'last_name' => 'Kruy'],
            ['first_name' => 'Sreylin', 'last_name' => 'Loun'],
            ['first_name' => 'Stephanie', 'last_name' => 'Lu'],
            ['first_name' => 'Vanhong', 'last_name' => 'Luy'],
            ['first_name' => 'Kimlong', 'last_name' => 'Neng'],
            ['first_name' => 'Sereywattana', 'last_name' => 'Pich'],
            ['first_name' => 'Saingiy', 'last_name' => 'Rat'],
            ['first_name' => 'Manin', 'last_name' => 'Samat'],
            ['first_name' => 'Chanleap', 'last_name' => 'Say'],
            ['first_name' => 'Puthisom', 'last_name' => 'Sear'],
            ['first_name' => 'Siriphort', 'last_name' => 'Sokhun'],
            ['first_name' => 'Rothtisa', 'last_name' => 'Song'],
            ['first_name' => 'Uylong', 'last_name' => 'Song'],
            ['first_name' => 'Theanaraksa', 'last_name' => 'Srorn'],
            ['first_name' => 'Seakpanhaseth', 'last_name' => 'Teng'],
            ['first_name' => 'Pagnavitou', 'last_name' => 'Thong'],
            ['first_name' => 'Amaracaitly', 'last_name' => 'Uk'],
        ];

        foreach ($users as $userData) {
            $firstName = $userData['first_name'];
            $lastName = $userData['last_name'];
            $email = strtolower($firstName . $lastName . '@gmail.com');

            // Check if user already exists
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'password_hash' => Hash::make('11111111'),
                    'role' => 'user',
                    'is_active' => true,
                    'phone' => '010' . rand(100000, 999999), // Random phone number
                ]);
            }
        }

        $this->command->info('Regular user accounts created successfully!');
    }
}

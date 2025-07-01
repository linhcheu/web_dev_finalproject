<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create platform admin in admins_table ONLY
        Admin::updateOrCreate(
            ['email' => 'admin@careconnect.com'],
            [
                'username' => 'admin',
                'password_hash' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'profile_picture' => null,
            ]
        );
    }
}

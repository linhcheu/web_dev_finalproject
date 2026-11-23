<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Creates the platform admin for the admin panel.
     * Set ADMIN_EMAIL / ADMIN_PASSWORD in .env (config/careconnect.php) - always in production.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => config('careconnect.admin.email')],
            [
                'username' => 'admin',
                'password_hash' => Hash::make(config('careconnect.admin.password')),
            ]
        );
    }
}

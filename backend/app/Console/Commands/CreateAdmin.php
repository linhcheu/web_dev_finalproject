<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {email} {password} {--username=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $username = $this->option('username');

        // Check if admin already exists
        if (Admin::where('email', $email)->exists()) {
            $this->error('Admin with this email already exists!');
            return 1;
        }

        // Create admin
        Admin::create([
            'username' => $username,
            'email' => $email,
            'password_hash' => Hash::make($password),
            'can_manage_roles' => true,
            'profile_picture' => null,
        ]);

        $this->info('Admin account created successfully!');
        $this->info("Email: {$email}");
        $this->info("Username: {$username}");
        $this->info("Password: {$password}");

        return 0;
    }
} 
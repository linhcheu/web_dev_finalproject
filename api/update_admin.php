<?php

require 'vendor/autoload.php';
use Illuminate\Support\Facades\Hash;

// Properly bootstrap Laravel
$app = require 'bootstrap/app.php';

// Boot the application
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== UPDATING HOSPITAL ADMIN PASSWORDS ===\n";

// Update all hospital admin passwords to a known value for testing
$hospitalAdmins = App\Models\frontendModels\User::where('role', 'hospital_admin')->get();
$newPassword = 'admin123'; // Set a known password for all admins

foreach ($hospitalAdmins as $admin) {
    // Check if they have a hospital
    $hospital = App\Models\frontendModels\Hospital::where('owner_id', $admin->user_id)->first();
    
    if ($hospital) {
        echo "Updating password for: {$admin->first_name} {$admin->last_name} ({$admin->email})\n";
        echo "  Hospital: {$hospital->name}\n";
        
        // Update password
        $admin->password_hash = Hash::make($newPassword);
        $admin->save();
        
        echo "  ✅ Password updated to: {$newPassword}\n\n";
    } else {
        echo "Skipping {$admin->first_name} {$admin->last_name} - No hospital assigned\n\n";
    }
}

echo "=== READY FOR TESTING ===\n";
echo "You can now login with these hospital admin accounts:\n";
echo "Password for all: {$newPassword}\n\n";

$adminWithHospitals = App\Models\frontendModels\User::where('role', 'hospital_admin')
    ->whereIn('user_id', function($query) {
        $query->select('owner_id')->from('hospitals_table');
    })->get();

foreach ($adminWithHospitals as $admin) {
    $hospital = App\Models\frontendModels\Hospital::where('owner_id', $admin->user_id)->first();
    echo "📧 Email: {$admin->email}\n";
    echo "🏥 Hospital: {$hospital->name}\n";
    echo "🔑 Password: {$newPassword}\n";
    echo "---\n";
}

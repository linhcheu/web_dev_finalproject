<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

class DeleteAppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get current count before deletion
        $countBefore = DB::table('appointments')->count();
        $this->command->info("Current appointment count: {$countBefore}");

        // Option 1: Delete a specific number of appointments (newest first)
        $deleteCount = 300; // Number of appointments to delete
        
        $appointments = Appointment::latest()->take($deleteCount)->get();
        $idsToDelete = $appointments->pluck('appointment_id')->toArray();
        
        // Using batch deletion to improve performance
        if (count($idsToDelete) > 0) {
            Appointment::whereIn('appointment_id', $idsToDelete)->delete();
            $this->command->info("Deleted {$deleteCount} appointments");
        } else {
            $this->command->info("No appointments found to delete");
        }

        // Get count after deletion to verify
        $countAfter = DB::table('appointments')->count();
        $this->command->info("Appointment count after deletion: {$countAfter}");
        $this->command->info("Total appointments deleted: " . ($countBefore - $countAfter));
    }
}

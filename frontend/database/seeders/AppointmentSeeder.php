<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hospital;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $hospitals = Hospital::where('status', 'active')->get();
        
        if ($users->isEmpty() || $hospitals->isEmpty()) {
            $this->command->error('Cannot create appointments: No users or active hospitals found');
            return;
        }

        $symptoms = [
            'Fever and headache',
            'Persistent cough',
            'Stomach pain and nausea',
            'Joint pain in knee',
            'Skin rash on arms',
            'Sore throat and difficulty swallowing',
            'Back pain',
            'Shortness of breath',
            'Chest pain',
            'Dizziness and fatigue',
            'Ear pain',
            'Eye irritation',
            'Toothache',
            'Frequent urination',
            'Muscle weakness',
            'Swollen lymph nodes',
            'Allergic reaction',
            'High blood pressure',
            'Migraine',
            'Anxiety symptoms'
        ];

        $statuses = ['upcoming', 'completed'];
        
        // Time slots available for appointments (9 AM to 5 PM)
        $timeSlots = [];
        $startTime = Carbon::createFromTime(9, 0, 0);
        $endTime = Carbon::createFromTime(17, 0, 0);
        
        while ($startTime <= $endTime) {
            $timeSlots[] = $startTime->format('H:i:s');
            $startTime->addMinutes(30);
        }

        // Generate dates: 50% past appointments, 50% future appointments
        $dates = [];
        
        // Past dates (last 30 days)
        for ($i = 1; $i <= 30; $i++) {
            $dates[] = Carbon::now()->subDays($i)->format('Y-m-d');
        }
        
        // Future dates (next 30 days)
        for ($i = 1; $i <= 30; $i++) {
            $dates[] = Carbon::now()->addDays($i)->format('Y-m-d');
        }
        
        // Create 100 random appointments
        $appointmentCount = 100;
        $createdCount = 0;
        
        $this->command->info("Creating {$appointmentCount} appointments...");
        
        // Progress bar for visual feedback
        $bar = $this->command->getOutput()->createProgressBar($appointmentCount);
        $bar->start();

        while ($createdCount < $appointmentCount) {
            $user = $users->random();
            $hospital = $hospitals->random();
            $date = $dates[array_rand($dates)];
            $time = $timeSlots[array_rand($timeSlots)];
            $symptom = $symptoms[array_rand($symptoms)];
            
            // Set status based on date
            $appointmentDate = Carbon::parse($date);
            if ($appointmentDate->isPast()) {
                // Past appointments are either completed or no_show
                $status = rand(0, 5) > 0 ? 'completed' : 'no_show'; // 5/6 chance of completed
            } else {
                // Future appointments are upcoming
                $status = 'upcoming';
            }
            
            // Check if this appointment slot is already taken at this hospital
            $existing = Appointment::where('hospital_id', $hospital->hospital_id)
                ->where('appointment_date', $date)
                ->where('appointment_time', $time)
                ->first();
                
            if (!$existing) {
                Appointment::create([
                    'user_id' => $user->user_id,
                    'hospital_id' => $hospital->hospital_id,
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                    'patient_phone' => $user->phone ?? '010' . rand(100000, 999999),
                    'symptom' => $symptom,
                    'status' => $status
                ]);
                
                $createdCount++;
                $bar->advance();
            }
        }
        
        $bar->finish();
        $this->command->newLine(2);
        $this->command->info('Appointments created successfully!');
    }
}

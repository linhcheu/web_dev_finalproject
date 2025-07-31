<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\frontendModels\User;
use App\Models\frontendModels\Hospital;
use Carbon\Carbon;

class HospitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            'Phnom Penh', 'Siem Reap', 'Battambang', 'Sihanoukville', 
            'Kampot', 'Kep', 'Kandal', 'Takeo', 'Kampong Cham'
        ];

        $locations = [
            'No. 123, St. 271, Sangkat Tuol Tumpung Temuoy, Khan Chamkarmorn',
            'No. 456, St. 134, Sangkat Phsar Thmey, Khan Daun Penh',
            'No. 789, St. 51, Sangkat Boeung Keng Kang, Khan Chamkarmorn',
            'No. 321, St. 598, Sangkat Boeung Kak, Khan Tuol Kork',
            'No. 654, St. 271, Sangkat Boeng Salang, Khan Tuol Kork',
            'No. 987, St. 2004, Sangkat Kakab, Khan Por Senchey',
            'No. 234, St. 93, Sangkat Srah Chak, Khan Daun Penh',
            'No. 567, St. 118, Sangkat Phsar Chas, Khan Daun Penh',
            'No. 890, St. 352, Sangkat Boeung Keng Kang, Khan Chamkarmorn'
        ];

        $contactInfos = [
            'Tel: 023 123 456, Email: info@{hospital}.com',
            'Hotline: 012 345 678, Emergency: 012 987 654',
            'Reception: 023 456 789, WhatsApp: 078 123 456',
            'Phone: 078 789 012, Telegram: @{hospital}',
            'Emergency: 023 789 012, Appointments: 087 123 456'
        ];

        $hospitalInfos = [
            'A leading healthcare provider offering comprehensive medical services.',
            'Specialized in emergency and critical care with state-of-the-art equipment.',
            'Providing quality healthcare services to the community since 1995.',
            'A modern hospital with international standard facilities and experienced doctors.',
            'Committed to delivering exceptional patient care and medical excellence.',
            'A trusted name in healthcare with a focus on patient-centered approach.',
            'Offering a wide range of medical specialties and personalized care.',
            'Equipped with advanced technology and dedicated medical professionals.',
            'A healthcare facility that combines expertise with compassionate care.'
        ];

        $plans = ['basic', 'premium', 'enterprise'];

        // Create hospitals and link them to admin users
        $hospitalAdmins = User::where('role', 'hospital_admin')->get();

        foreach ($hospitalAdmins as $admin) {
            // Extract hospital name from admin first name and last name
            $hospitalName = '';
            
            if ($admin->last_name == 'Hospital') {
                $hospitalName = $admin->first_name . ' Hospital';
            } elseif ($admin->last_name == 'Clinic') {
                $hospitalName = $admin->first_name . ' Clinic';
            } elseif ($admin->last_name == 'Medical') {
                $hospitalName = $admin->first_name . ' Medical Center';
            } elseif ($admin->last_name == 'Pediatric') {
                $hospitalName = $admin->first_name . ' Pediatric Hospital';
            } else {
                $hospitalName = $admin->first_name . ' ' . $admin->last_name;
            }
            
            // Check if hospital already exists for this admin
            $existingHospital = Hospital::where('owner_id', $admin->user_id)->first();
            
            if (!$existingHospital) {
                // Create hospital
                $hospital = Hospital::create([
                    'name' => $hospitalName,
                    'province' => $provinces[array_rand($provinces)],
                    'location' => $locations[array_rand($locations)],
                    'contact_info' => str_replace('{hospital}', strtolower($admin->first_name), $contactInfos[array_rand($contactInfos)]),
                    'information' => $hospitalInfos[array_rand($hospitalInfos)],
                    'owner_id' => $admin->user_id,
                    'status' => 'active',
                ]);
                
                // Create subscription
                $plan = $plans[array_rand($plans)];
                $price = ($plan === 'basic') ? 9.99 : (($plan === 'premium') ? 29.99 : 99.99);
                
                $hospital->subscriptions()->create([
                    'plan_type' => $plan,
                    'price' => $price,
                    'start_date' => Carbon::now(),
                    'end_date' => Carbon::now()->addYear(),
                    'status' => 'active',
                    'auto_renew' => true,
                ]);
            }
        }

        $this->command->info('Hospitals created successfully with active subscriptions!');
    }
}

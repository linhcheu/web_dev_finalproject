<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Feedback;
use App\Models\Hospital;

class PositiveFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds for positive feedback.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $hospitals = Hospital::where('status', 'active')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('Cannot create feedback: No regular users found');
            return;
        }

        $positiveFeedbackComments = [
            "Exceptional care at this hospital! The staff was not only professional but also incredibly kind and attentive throughout my entire stay.",
            
            "I cannot praise the doctors enough. They took the time to thoroughly explain my condition and treatment options in a way that was easy to understand.",
            
            "The facilities are immaculate and modern. Everything from the waiting areas to the examination rooms was spotlessly clean and well-maintained.",
            
            "What impressed me most was how the staff worked together as a team. The coordination between doctors, nurses, and administrative staff was seamless.",
            
            "This hospital truly sets the standard for healthcare in Cambodia. From admission to discharge, every step was handled with the utmost care and efficiency.",
            
            "The nurses were angels in disguise! They checked on me regularly, always with a smile, and made sure I was comfortable and well-informed.",
            
            "I was amazed by how quickly I was seen. Despite the busy hospital, the wait time was minimal, and the care I received was unhurried and thorough.",
            
            "The technology at this hospital is cutting-edge. All the diagnostic equipment appeared to be state-of-the-art, giving me confidence in my diagnosis.",
            
            "I've been to many hospitals, but the level of personal attention here was extraordinary. I felt like a person, not just a patient number.",
            
            "The doctor who treated me was not only knowledgeable but also compassionate. He listened to all my concerns without rushing me.",
            
            "Even the administrative process was smooth! Registration was quick, billing was transparent, and the staff helped me understand my insurance coverage.",
            
            "I'm grateful for the follow-up care. The hospital called me two days after my visit to check on my recovery progress.",
            
            "The pediatric unit deserves special mention. They made my child feel at ease during what could have been a scary experience.",
            
            "I appreciated how the doctor consulted with specialists to ensure the best care plan for my complex condition. Very thorough!",
            
            "The emergency room staff was calm and efficient during a very stressful situation. They saved my father's life with their quick response.",
            
            "As someone with anxiety about medical procedures, I was touched by how the staff took extra time to calm my nerves before my surgery.",
            
            "The hospital's cleanliness is impressive. Even the bathrooms were spotless, which says a lot about their attention to detail.",
            
            "The food was surprisingly good! It was nutritious, flavorful, and they accommodated my dietary restrictions without any issues.",
            
            "I was able to book my appointment through their online system, which was convenient and user-friendly. The whole process was efficient from start to finish.",
            
            "The pharmacy in the hospital had all the medications I needed, and the pharmacist took time to explain how to take them properly.",
            
            "I felt safe throughout my stay. The security measures were thorough but not intrusive, and staff ID checks were consistent.",
            
            "The waiting area was comfortable with good amenities—free WiFi, water dispensers, and current magazines made the short wait pleasant.",
            
            "The billing department was extraordinarily helpful. They worked with me to create a payment plan that fit my budget when I was concerned about costs.",
            
            "I was impressed by the hospital's environmental consciousness—they had clear recycling systems and seemed to minimize waste where possible.",
            
            "The specialist I saw was clearly at the top of their field. They were aware of the latest research and treatment options for my condition.",
            
            "Even during peak hours, the hospital maintained a calm, organized atmosphere that helped reduce my stress during my visit.",
            
            "The reception staff greeted everyone with a warm smile and genuine concern, setting a positive tone for the entire experience.",
            
            "My surgeon's skill was evident in my quick recovery and minimal scarring. I couldn't be more pleased with the results.",
            
            "The hospital offered translation services for my elderly relative who doesn't speak English well. This attention to communication needs was impressive.",
            
            "I appreciated being included in the decision-making process for my treatment. The doctors presented options and respected my choices.",
            
            "The physiotherapy department was outstanding. My therapist created a personalized recovery plan and encouraged me throughout the process.",
            
            "Despite being admitted on a weekend, I received the same high quality of care as I would expect during regular hours.",
            
            "The children's waiting area was thoughtfully designed with toys, books, and colorful decorations that kept my kids entertained during our wait.",
            
            "I was concerned about hospital-acquired infections, but their hygiene protocols were visible and reassuring—hand sanitizing stations everywhere and staff consistently washing hands.",
            
            "The hospital's app allowed me to access my test results and communicate with my doctor easily after my visit, which was very convenient.",
            
            "The maternity ward nurses were exceptional—they provided invaluable guidance and support during the challenging first days with my newborn.",
            
            "When I was diagnosed with a chronic condition, the hospital connected me with support groups and additional resources, going beyond just medical treatment.",
            
            "The hospital's quiet hours policy was well-enforced, allowing for proper rest during my overnight stay.",
            
            "I never felt rushed during consultations. My doctor spent a full 30 minutes discussing my health concerns and answering all my questions.",
            
            "The wheelchair assistance was readily available and the staff who helped transport me were courteous and attentive to my comfort.",
            
            "I was impressed that the doctor called me personally with my test results rather than having an assistant do it.",
            
            "The hospital's preventive care program has been invaluable for managing my health proactively rather than just treating problems as they arise.",
            
            "During a power outage, the hospital's backup systems kicked in seamlessly with no disruption to care—very reassuring!",
            
            "The ICU nurses treating my mother were not only clinically excellent but also provided emotional support to our family during a difficult time.",
            
            "The hospital's charity program helped cover costs when I was going through financial hardship. Their compassion made a huge difference in my life.",
            
            "I've been coming to this hospital for years and have always received consistent, high-quality care from every department.",
            
            "The staff remembered my name even though it had been months since my last visit—that personal touch means a lot.",
            
            "The hospital's vaccination clinic was efficiently run with minimal waiting and clear information about potential side effects.",
            
            "Their community health programs show they care about more than just treating illness—they're actively working to improve public health.",
            
            "I've recommended this hospital to all my friends and family because my experiences have been consistently positive over many years.",
            
            "The radiology technician was gentle and patient, taking extra care since I was nervous about my first MRI.",
            
            "I appreciate that the doctors at this hospital take a holistic approach, considering lifestyle factors and not just prescribing medications.",
            
            "The staff helped me navigate the complex healthcare system, coordinating between different specialists for my multifaceted condition.",
            
            "This hospital restored my faith in healthcare. After disappointing experiences elsewhere, I finally found doctors who truly listen.",
            
            "The attention to patient dignity was notable—extra care was taken with draping and privacy during examinations.",
            
            "Even the janitorial staff was friendly and professional, contributing to the overall positive atmosphere of the hospital.",
            
            "My elderly father who is hard of hearing was treated with extra patience and respect, with staff ensuring he understood all instructions.",
            
            "The hospital's patient portal is well-designed and makes it easy to schedule appointments, view records, and communicate with my healthcare team.",
            
            "I received a follow-up call checking on my medication side effects, which caught and resolved an issue before it became serious.",
            
            "The cafeteria offered healthy, affordable options—I was pleasantly surprised by the quality of food available to visitors.",
            
            "The hospital's cultural sensitivity was evident in how they accommodated various religious and cultural needs of patients.",
            
            "The pain management team was responsive and effective, regularly assessing my comfort level and adjusting treatment accordingly.",
        ];

        $this->command->info("Creating positive feedback entries...");
        $bar = $this->command->getOutput()->createProgressBar(count($positiveFeedbackComments));
        $bar->start();
        
        // Get random hospital names to include in some comments
        $hospitalNames = $hospitals->pluck('name')->toArray();
        
        foreach ($positiveFeedbackComments as $index => $comment) {
            // Pick a random user for each feedback
            $user = $users->random();
            
            // For some comments, include specific hospital names to make them more authentic
            if ($index % 5 === 0 && !empty($hospitalNames)) {
                $hospitalName = $hospitalNames[array_rand($hospitalNames)];
                $comment = str_replace("this hospital", "$hospitalName", $comment);
                $comment = str_replace("The hospital", "$hospitalName", $comment);
            }
            
            Feedback::create([
                'user_id' => $user->user_id,
                'comments' => $comment,
                'created_at' => now()->subDays(rand(1, 60)) // Spread the feedback over the last 2 months
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->command->newLine(2);
        $this->command->info('Positive feedback created successfully! These entries will help balance the negative feedback.');
    }
}

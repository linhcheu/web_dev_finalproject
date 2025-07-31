<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Feedback;

class BadFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds for negative feedback.
     */
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        
        if ($users->isEmpty()) {
            $this->command->error('Cannot create feedback: No regular users found');
            return;
        }

        $badFeedbackComments = [
            "Terrible experience at this hospital. Had to wait over 4 hours to see a doctor and they still couldn't diagnose my problem correctly.",
            
            "The staff was incredibly rude and dismissive of my concerns. I felt like I was bothering them by seeking healthcare.",
            
            "Horrific sanitation standards! I saw blood stains on the floor and the examination room didn't look like it had been cleaned in days.",
            
            "Completely incompetent doctors. They prescribed medication that I'm allergic to despite it being clearly noted in my records.",
            
            "Worst hospital I've ever visited. The reception staff was unhelpful, the doctors were late, and I was charged for services I never received.",
            
            "Avoid this place at all costs! I went in with a minor issue and left with an infection from their unsanitary conditions.",
            
            "The doctor spent less than 2 minutes with me and didn't bother to listen to my symptoms. Just rushed to prescribe something and get me out.",
            
            "Unprofessional staff who spent more time chatting with each other than attending to patients. Disgusting facilities too.",
            
            "My elderly parent was neglected for hours. No one checked on them despite repeated requests for assistance.",
            
            "Outrageous billing practices! They charged me three times what I was quoted and refused to provide an itemized bill.",
            
            "The nurses were gossiping about patients. I could literally hear them discussing private medical information openly.",
            
            "They lost my test results and then tried to blame ME for it! Had to redo painful tests because of their incompetence.",
            
            "Misdiagnosed my condition which led to weeks of unnecessary suffering. When I went elsewhere, I was correctly diagnosed in minutes.",
            
            "The hospital rooms were freezing cold and they refused to provide additional blankets despite multiple requests.",
            
            "Medication errors that could have been life-threatening. The nurse tried to give me someone else's medication!",
            
            "The wait time was so excessive that my condition worsened significantly while waiting. Complete disregard for urgency.",
            
            "Found a cockroach in the examination room. When I reported it, staff just shrugged it off like it was normal.",
            
            "The doctor was checking their phone during my consultation and clearly wasn't listening to anything I said.",
            
            "I was billed for a specialist consultation that never happened. When I disputed it, they threatened to send it to collections.",
            
            "The entire experience was dehumanizing. I felt like I was on an assembly line rather than receiving personalized care."
        ];

        $this->command->info("Creating negative feedback entries...");
        
        foreach ($badFeedbackComments as $comment) {
            // Pick a random user for each feedback
            $user = $users->random();
            
            Feedback::create([
                'user_id' => $user->user_id,
                'comments' => $comment
            ]);
        }

        $this->command->info('Negative feedback created successfully!');
    }
}

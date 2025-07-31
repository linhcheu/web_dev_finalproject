<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\Feedback;

class HomeController extends Controller
{
    public function index()
    {
        // Get active hospitals for popular hospitals section
        $popularHospitals = Hospital::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        
        // Get 10 recent positive feedback entries for the feedback section
        $recentFeedback = Feedback::with('user')
            ->join('users_table', 'feedback.user_id', '=', 'users_table.user_id')
            ->orderBy('feedback.created_at', 'desc')
            ->take(10)
            ->get();
        
        return view('home', compact('popularHospitals', 'recentFeedback'));
    }
    
    public function about()
    {
        return view('about');
    }
    
    public function feedback()
    {
        $userFeedback = auth()->user()->feedback()->latest()->get();
        return view('feedback', compact('userFeedback'));
    }
    
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);
        
        auth()->user()->feedback()->create([
            'comments' => $request->comments
        ]);
        
        return redirect()->route('feedback')->with('success', 'Feedback submitted successfully!');
    }
}

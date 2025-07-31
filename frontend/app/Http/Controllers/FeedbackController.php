<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        return view('user.feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'comments' => 'required|string|min:10|max:1000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'comments' => $request->comments,
        ]);

        return redirect()->route('user.feedback')->with('success', 'Thank you for your feedback! We appreciate your input.');
    }

    public function hospitalIndex()
    {
        return view('hospital.feedback');
    }

    /**
     * Store a new feedback from a hospital admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function hospitalStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'comments' => 'required|string|min:5|max:1000',
        ]);

        // Create the feedback
        Feedback::create([
            'user_id' => auth()->id(),
            'comments' => $request->comments,
        ]);

        // Redirect back with success message
        return redirect()->back()->with('success', 'Feedback submitted successfully!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index()
    {
        $totalFeedback = Feedback::count();
        $feedback = Feedback::with('user')->latest()->paginate(10);

        return view('feedback', compact('totalFeedback', 'feedback'));
    }
} 
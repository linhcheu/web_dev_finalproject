<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $totalFeedback = Feedback::count();
        $feedback = Feedback::with('user')->latest()->paginate(10);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'feedback' => $feedback->items(),
                    'pagination' => [
                        'current_page' => $feedback->currentPage(),
                        'last_page' => $feedback->lastPage(),
                        'per_page' => $feedback->perPage(),
                        'total' => $feedback->total()
                    ],
                    'total_feedback' => $totalFeedback
                ]
            ]);
        }

        return view('feedback', compact('totalFeedback', 'feedback'));
    }

    public function show(Request $request, $id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'feedback' => [
                        'id' => $feedback->feedback_id,
                        'comments' => $feedback->comments,
                        'created_at' => $feedback->created_at,
                        'user' => $feedback->user ? [
                            'id' => $feedback->user->user_id,
                            'name' => $feedback->user->first_name . ' ' . $feedback->user->last_name,
                            'email' => $feedback->user->email
                        ] : null
                    ]
                ]
            ]);
        }

        return view('feedback_show', compact('feedback'));
    }

    public function statistics(Request $request)
    {
        $totalFeedback = Feedback::count();
        $todayFeedback = Feedback::whereDate('created_at', today())->count();
        $thisWeekFeedback = Feedback::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthFeedback = Feedback::whereMonth('created_at', now()->month)->count();

        $stats = [
            'total' => $totalFeedback,
            'today' => $todayFeedback,
            'this_week' => $thisWeekFeedback,
            'this_month' => $thisMonthFeedback
        ];

        // Always return JSON for statistics
        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $stats
            ]
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully!'
        ]);
    }
} 
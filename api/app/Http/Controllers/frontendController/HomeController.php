<?php

namespace App\Http\Controllers\frontendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Hospital;
use App\Models\frontendModels\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Get homepage data including popular hospitals and feedback
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
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

        return response()->json([
            'success' => true,
            'data' => [
                'popularHospitals' => $popularHospitals,
                'recentFeedback' => $recentFeedback->map(function ($feedback) {
                    return [
                        'id' => $feedback->feedback_id,
                        'comments' => $feedback->comments,
                        'created_at' => $feedback->created_at,
                        'user' => [
                            'id' => $feedback->user->user_id,
                            'first_name' => $feedback->user->first_name,
                            'last_name' => $feedback->user->last_name,
                            'role' => $feedback->user->role,
                            'profile_picture' => $feedback->user->profile_picture
                        ]
                    ];
                })
            ]
        ]);
    }

    public function about()
    {
        return response()->json([
            'about' => 'CareConnect - Connecting Users with Hospitals. Your trusted platform for healthcare.'
        ]);
    }

    public function feedback()
    {
        $user = Auth::user();
        $userFeedback = Feedback::where('user_id', $user->user_id)->latest()->get();
        return response()->json([
            'userFeedback' => $userFeedback
        ]);
    }

    public function storeFeedback(Request $request)
    {
        $request->validate([
            'comments' => 'required|string|max:1000'
        ]);
        
        $user = Auth::user();
        Feedback::create([
            'user_id' => $user->user_id,
            'comments' => $request->comments
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully!'
        ]);
    }

    public function subscriptionPlans()
    {
        $plans = [
            'basic' => [
                'name' => 'Basic Plan',
                'price' => 99,
                'duration' => '1 month',
                'features' => [
                     'Listing in hospital directory',
                    'Appointment management',
                    'Analytics dashboard'
                ]
            ],
            'premium' => [
                'name' => 'Premium Plan',
                'price' => 199,
                'duration' => '3 months',
                'features' => [
                    'Listing in hospital directory',
                    'Appointment management',
                    'Analytics dashboard'

                ]
            ],
            'enterprise' => [
                'name' => 'Enterprise Plan',
                'price' => 399,
                'duration' => '6 months',
                'features' => [
                      'Listing in hospital directory',
                    'Appointment management',
                    'Analytics dashboard'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'plans' => $plans
            ]
        ]);
    }
}

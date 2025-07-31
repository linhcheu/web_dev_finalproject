<?php

namespace App\Http\Controllers\frontendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Display a listing of feedback.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // If user is authenticated, get their feedback
            if ($request->user()) {
                $feedback = Feedback::where('user_id', $request->user()->user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                // Otherwise get all feedback (since there's no is_public column)
                $feedback = Feedback::orderBy('created_at', 'desc')
                    ->get();
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'feedback' => $feedback
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created feedback or update existing one.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log the incoming request data for debugging
            Log::info('Feedback submission data', [
                'all_data' => $request->all()
            ]);

            // Check if we're updating existing feedback
            $isUpdate = $request->has('feedback_id');
            
            if ($isUpdate) {
                // Validate for update
                $validated = $request->validate([
                    'feedback_id' => 'required|exists:feedback,feedback_id',
                    'comments' => 'required|string|max:500'
                ]);
                
                // Find the feedback
                $feedback = Feedback::findOrFail($validated['feedback_id']);
                
                // Check if user owns this feedback
                if ($request->user() && $feedback->user_id != $request->user()->user_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized action',
                        'error' => 'You can only update your own feedback'
                    ], 403);
                }
                
                // Update only the comments field which exists in the table
                $feedback->update([
                    'comments' => $validated['comments']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback updated successfully',
                    'data' => [
                        'feedback' => $feedback
                    ]
                ]);
            } else {
                // For new feedback, validate required fields
                $validated = $request->validate([
                    'comments' => 'required|string|max:500'
                ]);
                
                // Set user_id if authenticated
                if ($request->user()) {
                    $validated['user_id'] = $request->user()->user_id;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Authentication required',
                        'error' => 'You must be logged in to submit feedback'
                    ], 401);
                }
                
                // Create feedback with only the fields that exist in the table
                $feedback = Feedback::create([
                    'user_id' => $validated['user_id'],
                    'comments' => $validated['comments']
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Feedback submitted successfully',
                    'data' => [
                        'feedback' => $feedback
                    ]
                ], 201);
            }
        } catch (\Exception $e) {
            // Detailed error response for debugging
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback',
                'error' => $e->getMessage(),
                'trace' => env('APP_DEBUG') ? $e->getTrace() : null,
                'request_data' => $request->all()
            ], 500);
        }
    }

    /**
     * Get feedback for a specific user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserFeedback(Request $request): JsonResponse
    {
        try {
            // Get the authenticated user
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required',
                    'error' => 'You must be logged in to view your feedback'
                ], 401);
            }
            
            // Get all feedback for this user
            $feedback = Feedback::where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => [
                    'feedback' => $feedback
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve user feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store feedback for a hospital (same as store since we don't have hospital_id).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function hospitalStore(Request $request): JsonResponse
    {
        // Use the same store method since we don't track hospital_id
        return $this->store($request);
    }
}
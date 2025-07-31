<?php

namespace App\Http\Controllers\backendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\Feedback;
use App\Models\frontendModels\User;
use Illuminate\Support\Facades\DB;

class FeedbackController extends Controller
{
    /**
     * Display a listing of all feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get total feedback count
        $totalFeedback = Feedback::count();
        
        // Query builder for feedback with user relationship
        $query = Feedback::with('user');
        
        // Handle search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comments', 'like', "%{$search}%");
        }
        
        // Handle sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results
        $perPage = $request->input('per_page', 10);
        $feedback = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_feedback' => $totalFeedback,
                'feedback' => $feedback,
                'pagination' => [
                    'current_page' => $feedback->currentPage(),
                    'last_page' => $feedback->lastPage(),
                    'per_page' => $feedback->perPage(),
                    'total' => $feedback->total()
                ]
            ]
        ]);
    }
    
    /**
     * Get feedback details for a specific entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $feedback = Feedback::with('user')->find($id);
        
        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $feedback
        ]);
    }
    
    /**
     * Delete a feedback entry
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $feedback = Feedback::find($id);
        
        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }
        
        $feedback->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully'
        ]);
    }
    
    /**
     * Get statistics about feedback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics()
    {
        $totalFeedback = Feedback::count();
        $recentFeedback = Feedback::with('user')->latest()->take(5)->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_feedback' => $totalFeedback,
                'recent_feedback' => $recentFeedback
            ]
        ]);
    }
}
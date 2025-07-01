<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with('user');
        
        if ($request->user()->role !== 'hospital_admin') {
            $query->where('user_id', $request->user()->user_id);
        }
        
        $feedback = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $feedback
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'comments' => 'required|string',
        ]);

        $feedback = Feedback::create([
            'user_id' => $request->user()->user_id,
            'comments' => $request->comments
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'data' => $feedback->load('user')
        ], 201);
    }

    public function show(Feedback $feedback)
    {
        if ($feedback->user_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $feedback->load('user')
        ]);
    }

    public function update(Request $request, Feedback $feedback)
    {
        if ($feedback->user_id !== $request->user()->user_id && $request->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'comments' => 'sometimes|required|string',
        ]);

        $feedback->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Feedback updated successfully',
            'data' => $feedback->load('user')
        ]);
    }

    public function destroy(Feedback $feedback)
    {
        if ($feedback->user_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully'
        ]);
    }
} 
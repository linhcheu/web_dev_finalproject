<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscription::with('hospital');
        
        if ($request->user()->role !== 'hospital_admin') {
            // For regular users, show subscriptions for hospitals they own
            $query->whereHas('hospital', function($q) use ($request) {
                $q->where('owner_id', $request->user()->user_id);
            });
        }
        
        $subscriptions = $query->paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $subscriptions
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hospital_id' => 'required|exists:hospitals,hospital_id',
            'plan_type' => 'required|string|in:basic,premium,enterprise',
            'price' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'boolean'
        ]);

        $subscription = Subscription::create([
            'hospital_id' => $request->hospital_id,
            'plan_type' => $request->plan_type,
            'price' => $request->price,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'auto_renew' => $request->auto_renew ?? true,
            'status' => 'active'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'data' => $subscription->load('hospital')
        ], 201);
    }

    public function show(Subscription $subscription)
    {
        if ($subscription->hospital->owner_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $subscription->load('hospital')
        ]);
    }

    public function update(Request $request, Subscription $subscription)
    {
        if ($subscription->hospital->owner_id !== $request->user()->user_id && $request->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'hospital_id' => 'sometimes|required|exists:hospitals,hospital_id',
            'plan_type' => 'sometimes|required|string|in:basic,premium,enterprise',
            'price' => 'nullable|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|string|in:active,expired,cancelled',
            'auto_renew' => 'boolean'
        ]);

        $subscription->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Subscription updated successfully',
            'data' => $subscription->load('hospital')
        ]);
    }

    public function destroy(Subscription $subscription)
    {
        if ($subscription->hospital->owner_id !== request()->user()->user_id && request()->user()->role !== 'hospital_admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription deleted successfully'
        ]);
    }
} 
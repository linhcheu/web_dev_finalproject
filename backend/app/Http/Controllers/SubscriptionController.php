<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $totalSubscriptions = Subscription::count();
        $subscriptions = Subscription::with('hospital')->latest()->paginate(10);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subscriptions' => $subscriptions->items(),
                    'pagination' => [
                        'current_page' => $subscriptions->currentPage(),
                        'last_page' => $subscriptions->lastPage(),
                        'per_page' => $subscriptions->perPage(),
                        'total' => $subscriptions->total()
                    ],
                    'total_subscriptions' => $totalSubscriptions
                ]
            ]);
        }

        return view('subcription', compact('totalSubscriptions', 'subscriptions'));
    }

    public function show(Request $request, $id)
    {
        $subscription = Subscription::with('hospital')->findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'subscription' => [
                        'id' => $subscription->subscription_id,
                        'plan_type' => $subscription->plan_type,
                        'price' => $subscription->price,
                        'start_date' => $subscription->start_date,
                        'end_date' => $subscription->end_date,
                        'status' => $subscription->status,
                        'auto_renew' => $subscription->auto_renew,
                        'created_at' => $subscription->created_at,
                        'hospital' => $subscription->hospital ? [
                            'id' => $subscription->hospital->hospital_id,
                            'name' => $subscription->hospital->name,
                            'location' => $subscription->hospital->location
                        ] : null
                    ]
                ]
            ]);
        }

        return view('subscription_show', compact('subscription'));
    }

    public function edit($id)
    {
        $subscription = Subscription::with('user')->findOrFail($id);
        $users = User::all();
        
        return view('subscription_edit', compact('subscription', 'users'));
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $updateType = $request->input('update_type');

        switch ($updateType) {
            case 'basic_info':
                return $this->updateBasicInfo($request, $subscription);
            
            case 'plan_details':
                return $this->updatePlanDetails($request, $subscription);
            
            case 'status':
                return $this->updateStatus($request, $subscription);
            
            default:
                return redirect()->route('subscription.edit', $subscription->subscription_id)->with('error', 'Invalid update type.');
        }
    }

    public function destroy($id)
    {
        try {
            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscription deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateBasicInfo(Request $request, $subscription)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $subscription->update([
            'user_id' => $request->user_id,
        ]);

        return redirect()->route('subscription.edit', $subscription->subscription_id)->with('success', 'User information updated successfully!');
    }

    private function updatePlanDetails(Request $request, $subscription)
    {
        $request->validate([
            'plan_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        $subscription->update([
            'plan_name' => $request->plan_name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
        ]);

        return redirect()->route('subscription.edit', $subscription->subscription_id)->with('success', 'Plan details updated successfully!');
    }

    private function updateStatus(Request $request, $subscription)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,pending,expired,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $subscription->update([
            'status' => $request->status,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('subscription.edit', $subscription->subscription_id)->with('success', 'Subscription status updated successfully!');
    }
} 
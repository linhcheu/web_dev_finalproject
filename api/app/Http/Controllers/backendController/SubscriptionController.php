<?php

namespace App\Http\Controllers\backendController;

use App\Models\backendModels\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $totalSubscriptions = Subscription::count();
        
        // Change from with(['user', 'hospital']) to just with(['hospital'])
        // since 'user' relationship doesn't exist on Subscription model
        $subscriptions = Subscription::with(['hospital'])->latest()->paginate(10);
        
        $stats = [
            'total' => $totalSubscriptions,
            'active' => Subscription::where('status', 'active')->count(),
            'expired' => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => [
                'subscriptions' => $subscriptions,
                'stats' => $stats,
                'pagination' => [
                    'total' => $subscriptions->total(),
                    'per_page' => $subscriptions->perPage(),
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                ]
            ]
        ]);
    }

    public function edit($id)
    {
        $subscription = Subscription::with('user')->findOrFail($id);
        $users = User::all();
        return response()->json([
            'subscription' => $subscription,
            'users' => $users
        ]);
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
                return response()->json(['error' => 'Invalid update type.'], 400);
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
        return response()->json(['success' => true, 'message' => 'User information updated successfully!']);
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
        return response()->json(['success' => true, 'message' => 'Plan details updated successfully!']);
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
        return response()->json(['success' => true, 'message' => 'Subscription status updated successfully!']);
    }
}
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $subscriptions = Subscription::with('hospital:hospital_id,name,province')
            ->when(in_array($request->status, Subscription::STATUSES, true), fn ($q) => $q->where('status', $request->status))
            ->latest('subscription_id')
            ->paginate(10);

        return $this->paginated($subscriptions, ['total_all' => Subscription::count()]);
    }

    public function show(Subscription $subscription): JsonResponse
    {
        return $this->ok($subscription->load('hospital'));
    }

    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $subscription->update($request->validate([
            'plan_type' => ['sometimes', 'required', Rule::in(Subscription::PLANS)],
            'price' => 'sometimes|nullable|numeric|min:0|max:99999999',
            'status' => ['sometimes', 'required', Rule::in(Subscription::STATUSES)],
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'auto_renew' => 'sometimes|boolean',
        ]));

        return $this->ok($subscription->fresh('hospital'), 'Subscription updated successfully!');
    }

    public function destroy(Subscription $subscription): JsonResponse
    {
        $subscription->delete();

        return $this->ok(null, 'Subscription deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $subscriptions = $this->api->paginate('admin/subscriptions', Subscription::class, $request->only('status', 'page'));

        return view('subscription', [
            'subscriptions' => $subscriptions,
            'totalSubscriptions' => $subscriptions->extra['total_all'],
        ]);
    }

    public function edit(int $id): View
    {
        return view('subscription_edit', [
            'subscription' => Subscription::fromApi($this->api->get("admin/subscriptions/$id")['data']),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'plan_type' => 'required|in:basic,premium,enterprise',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,expired,cancelled',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'auto_renew' => 'nullable|boolean',
        ]);
        $data['auto_renew'] = $request->boolean('auto_renew');

        $this->api->put("admin/subscriptions/$id", $data);

        return redirect()->route('subscription.edit', $id)->with('success', 'Subscription updated successfully!');
    }

    public function destroy(int $id): JsonResponse
    {
        return response()->json(['success' => true] + $this->api->delete("admin/subscriptions/$id"));
    }
}

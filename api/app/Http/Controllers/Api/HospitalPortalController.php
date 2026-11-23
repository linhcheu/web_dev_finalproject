<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\Subscription;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * A hospital administrator manages their own hospital.
 */
class HospitalPortalController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return $this->ok($this->hospital($request)->load('subscription'));
    }

    public function dashboard(Request $request): JsonResponse
    {
        $hospital = $this->hospital($request);
        $appointments = fn () => $hospital->appointments();

        return $this->ok([
            'hospital' => $hospital->load('subscription'),
            'stats' => [
                'total_appointments' => $appointments()->count(),
                'upcoming_appointments' => $appointments()->upcoming()->count(),
                'today_appointments' => $appointments()->whereDate('appointment_date', today())->count(),
                'total_feedback' => $request->user()->feedback()->count(),
            ],
            'recent_appointments' => $appointments()->with('user')->latest()->take(5)->get(),
            'upcoming_appointments' => $appointments()->with('user')->upcoming()
                ->orderBy('appointment_date')->orderBy('appointment_time')->take(10)->get(),
        ]);
    }

    public function appointments(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'nullable|string|max:20',
            'status' => ['nullable', Rule::in(Appointment::STATUSES)],
        ]);

        $appointments = $this->hospital($request)->appointments()
            ->with('user')
            ->when($request->filled('phone'), fn ($q) => $q->where('patient_phone', 'like', '%'.$request->phone.'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return $this->paginated($appointments);
    }

    public function updateAppointment(Request $request, Appointment $appointment): JsonResponse
    {
        abort_unless($appointment->hospital_id === $this->hospital($request)->hospital_id, 404);

        $appointment->update($request->validate([
            'status' => ['required', Rule::in(Appointment::STATUSES)],
        ]));

        return $this->ok($appointment, 'Appointment updated successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $hospital = $this->hospital($request);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'required|string|max:2000',
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'hospital-profile-pictures', $hospital->profile_picture);
        }

        $hospital->update($data);

        return $this->ok($hospital->fresh('subscription'), 'Hospital information updated successfully.');
    }

    public function updateSubscription(Request $request): JsonResponse
    {
        $hospital = $this->hospital($request);
        $data = $request->validate(['plan_type' => ['required', Rule::in(Subscription::PLANS)]]);

        $hospital->subscriptions()->updateOrCreate(
            ['subscription_id' => $hospital->subscription?->subscription_id],
            $data + ['status' => 'active', 'start_date' => now(), 'end_date' => now()->addYear()],
        );

        return $this->ok($hospital->fresh('subscription'), 'Subscription updated successfully.');
    }

    public function cancelSubscription(Request $request): JsonResponse
    {
        $subscription = $this->hospital($request)->subscription;
        abort_unless($subscription, 404, 'No subscription found.');

        $subscription->update(['status' => 'cancelled']);

        return $this->ok($subscription, 'Subscription cancelled.');
    }

    private function hospital(Request $request): Hospital
    {
        return $request->user()->hospital ?? abort(404, 'No hospital is linked to this account.');
    }
}

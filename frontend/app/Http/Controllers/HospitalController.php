<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hospital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Public hospital pages + the hospital administrator's portal (/hospital/...).
 */
class HospitalController extends Controller
{
    public const PROVINCES = [
        'Banteay Meanchey', 'Battambang', 'Kampong Cham', 'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom',
        'Kampot', 'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri', 'Oddar Meanchey', 'Pailin',
        'Phnom Penh', 'Preah Sihanouk', 'Preah Vihear', 'Prey Veng', 'Pursat', 'Ratanakiri', 'Siem Reap',
        'Stung Treng', 'Svay Rieng', 'Takeo', 'Tbong Khmum',
    ];

    // ---- Public -------------------------------------------------------------

    public function index(Request $request): View
    {
        $request->validate(['province' => 'nullable|string|max:100']);

        return view('hospitals.index', [
            'hospitals' => Hospital::collectionFromApi($this->api->get('hospitals', $request->only('province'))['data']),
            'provinces' => self::PROVINCES,
        ]);
    }

    public function show(int $hospital): View
    {
        return view('hospitals.show', ['hospital' => Hospital::fromApi($this->api->get("hospitals/$hospital")['data'])]);
    }

    // ---- Hospital admin portal ---------------------------------------------

    public function dashboard(): View
    {
        $data = $this->api->get('my-hospital/dashboard')['data'];

        return view('hospital.dashboard', [
            'hospital' => Hospital::fromApi($data['hospital']),
            'stats' => $data['stats'],
            'recent_appointments' => Appointment::collectionFromApi($data['recent_appointments']),
            'upcoming_appointments' => Appointment::collectionFromApi($data['upcoming_appointments']),
        ]);
    }

    public function appointments(Request $request): View
    {
        return view('hospital.appointments', [
            'hospital' => $this->hospital(),
            'appointments' => $this->api->paginate('my-hospital/appointments', Appointment::class, $request->only('phone', 'status', 'page')),
        ]);
    }

    public function updateAppointment(Request $request, int $appointment): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:upcoming,completed,no_show']);

        $this->api->patch("my-hospital/appointments/$appointment", $data);

        return back()->with('success', 'Appointment updated successfully.');
    }

    public function profile(): View
    {
        return view('hospital.profile', ['hospital' => $this->hospital(), 'provinces' => self::PROVINCES]);
    }

    /** The profile page has three forms, told apart by "update_type". */
    public function updateProfile(Request $request): RedirectResponse
    {
        return match ($request->input('update_type')) {
            'password' => $this->updatePassword($request),
            'user_info' => $this->updateAdminInfo($request),
            'hospital_info' => $this->updateHospitalInfo($request),
            default => back()->with('error', 'Invalid update request.'),
        };
    }

    public function subscription(): View
    {
        $hospital = $this->hospital();

        return view('hospital.subscription', ['hospital' => $hospital, 'subscription' => $hospital->subscription]);
    }

    public function updateSubscription(Request $request): RedirectResponse
    {
        // Only the plan is forwarded. The demo card fields are never sent or stored.
        $data = $request->validate(['plan_type' => 'required|in:basic,premium,enterprise']);

        $this->api->put('my-hospital/subscription', $data);

        return back()->with('success', 'Subscription updated successfully.');
    }

    public function cancelSubscription(): RedirectResponse
    {
        $this->api->post('my-hospital/subscription/cancel');

        return back()->with('success', 'Subscription cancelled.');
    }

    private function updatePassword(Request $request): RedirectResponse
    {
        $this->api->put('me/password', $request->validate(ProfileController::passwordRules()));

        return back()->with('success', 'Password updated successfully.');
    }

    private function updateAdminInfo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'user_email' => 'required|email|max:100',
            'user_phone' => 'required|string|max:20',
            'user_profile_picture' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $user = $this->api->post('me', array_filter([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['user_email'],
            'phone' => $data['user_phone'],
            'profile_picture' => $data['user_profile_picture'] ?? null,
        ]), ['email' => 'user_email', 'phone' => 'user_phone', 'profile_picture' => 'user_profile_picture'])['data'];

        Auth::guard()->setAccount($user);

        return back()->with('success', 'User information updated successfully.');
    }

    private function updateHospitalInfo(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:500',
            'province' => 'required|string|max:100',
            'contact_info' => 'required|string|max:500',
            'information' => 'required|string|max:2000',
            'profile_picture' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $this->api->post('my-hospital', array_filter($data));

        return back()->with('success', 'Hospital information updated successfully.');
    }

    private function hospital(): Hospital
    {
        return Hospital::fromApi($this->api->get('my-hospital')['data']);
    }
}

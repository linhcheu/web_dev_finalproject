<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospital;
use App\Models\Appointment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class HospitalController extends Controller
{
    public function index(Request $request)
    {
        $query = Hospital::with('owner', 'subscriptions')->active()->withActiveSubscription();

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        $hospitals = $query->latest()->get();

        $provinces = [
            'Banteay Meanchey', 'Battambang', 'Kampong Cham', 'Kampong Chhnang', 'Kampong Speu', 'Kampong Thom',
            'Kampot', 'Kandal', 'Kep', 'Koh Kong', 'Kratie', 'Mondulkiri', 'Oddar Meanchey', 'Pailin',
            'Preah Vihear', 'Pursat', 'Prey Veng', 'Ratanakiri', 'Siem Reap', 'Preah Sihanouk', 'Stung Treng',
            'Svay Rieng', 'Takeo', 'Tbong Khmum', 'Phnom Penh'
        ];

        return view('hospitals.index', compact('hospitals', 'provinces'));
    }
    
    public function show(Hospital $hospital)
    {
        $hospital->load('owner', 'subscriptions');
        return view('hospitals.show', compact('hospital'));
    }
    
    public function dashboard()
    {
        $hospital = auth()->user()->hospital;
        
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        
        $stats = [
            'total_appointments' => $hospital->appointments()->count(),
            'upcoming_appointments' => $hospital->appointments()->upcoming()->count(),
            'today_appointments' => $hospital->appointments()->whereDate('appointment_date', today())->count(),
            'total_feedback' => $hospital->owner->feedback()->count(),
        ];
        
        $recent_appointments = $hospital->appointments()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();
            
        $upcoming_appointments = $hospital->appointments()
            ->with('user')
            ->upcoming()
            ->orderBy('appointment_date')
            ->take(10)
            ->get();
            
        return view('hospital.dashboard', compact('hospital', 'stats', 'recent_appointments', 'upcoming_appointments'));
    }
    
    public function appointments(Request $request)
    {
        $hospital = auth()->user()->hospital;
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        $query = $hospital->appointments()->with('user')->latest();
        
        // Filter by phone number
        if ($request->filled('phone')) {
            $query->where('patient_phone', 'like', '%' . $request->phone . '%');
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $appointments = $query->paginate(20);
        return view('hospital.appointments', compact('hospital', 'appointments'));
    }
    
    public function updateAppointment(Request $request, Appointment $appointment)
    {
        $hospital = auth()->user()->hospital;
        
        if (!$hospital || $appointment->hospital_id !== $hospital->hospital_id) {
            return back()->with('error', 'Unauthorized action.');
        }
        
        $request->validate([
            'status' => 'required|in:upcoming,completed'
        ]);
        
        $appointment->update([
            'status' => $request->status
        ]);
        
        return back()->with('success', 'Appointment updated successfully.');
    }
    
    public function profile()
    {
        $hospital = auth()->user()->hospital;
        
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        
        return view('hospital.profile', compact('hospital'));
    }
    
    public function updateProfile(Request $request)
    {
        $hospital = auth()->user()->hospital;
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        $user = auth()->user();
        $updateType = $request->input('update_type');

        if ($updateType === 'password') {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|string|min:6|confirmed',
            ], [
                'new_password.confirmed' => 'The new password confirmation does not match.'
            ]);
            if (!\Hash::check($request->input('current_password'), $user->password_hash)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
            $user->password = $request->input('new_password');
            $user->save();
            return back()->with('success', 'Password updated successfully.');
        }

        if ($updateType === 'user_info') {
            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'user_email' => 'required|email|max:255',
                'user_phone' => 'required|string|max:30',
                'user_profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            if ($request->hasFile('user_profile_picture')) {
                if ($user->profile_picture) {
                    \Storage::disk('public')->delete($user->profile_picture);
                }
                $userPath = $request->file('user_profile_picture')->store('profile-pictures', 'public');
                $user->profile_picture = $userPath;
            }
            $user->first_name = $request->input('first_name');
            $user->last_name = $request->input('last_name');
            $user->email = $request->input('user_email');
            $user->phone = $request->input('user_phone');
            $user->save();
            return back()->with('success', 'User information updated successfully.');
        }

        if ($updateType === 'hospital_info') {
            $request->validate([
                'name' => 'required|string|max:100',
                'location' => 'required|string',
                'province' => 'required|string|max:100',
                'contact_info' => 'required|string',
                'information' => 'required|string',
                'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            $updateData = $request->only(['name', 'location', 'province', 'contact_info', 'information']);
            if ($request->hasFile('profile_picture')) {
                if ($hospital->profile_picture) {
                    \Storage::disk('public')->delete($hospital->profile_picture);
                }
                $path = $request->file('profile_picture')->store('hospital-profile-pictures', 'public');
                $updateData['profile_picture'] = $path;
            }
            $hospital->update($updateData);
            return back()->with('success', 'Hospital information updated successfully.');
        }

        return back()->with('error', 'Invalid update request.');
    }
    
    public function subscription()
    {
        $hospital = auth()->user()->hospital;
        
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        
        $subscription = $hospital->subscriptions->first();
        
        return view('hospital.subscription', compact('hospital', 'subscription'));
    }
    
    public function updateSubscription(Request $request)
    {
        $hospital = auth()->user()->hospital;
        
        if (!$hospital) {
            return redirect()->route('home')->with('error', 'Hospital not found.');
        }
        
        $request->validate([
            'plan_type' => 'required|in:basic,premium,enterprise'
        ]);
        
        $subscription = $hospital->subscriptions->first();
        
        if ($subscription) {
            $subscription->update([
                'plan_type' => $request->plan_type,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear()
            ]);
        } else {
            $hospital->subscriptions()->create([
                'plan_type' => $request->plan_type,
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear()
            ]);
        }
        
        return back()->with('success', 'Subscription updated successfully.');
    }
    
    public function cancelSubscription()
    {
        $hospital = auth()->user()->hospital;
        if (!$hospital || !$hospital->subscriptions->first()) {
            return back()->with('error', 'No active subscription found.');
        }
        $hospital->subscriptions->first()->update(['status' => 'cancelled']);
        return back()->with('success', 'Subscription cancelled.');
    }
}

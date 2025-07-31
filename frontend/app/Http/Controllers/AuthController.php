<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Hospital;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (auth()->user()->role === 'hospital_admin') {
                return redirect()->route('hospital.dashboard');
            }
            
            return redirect()->intended(route('home'));
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users_table',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'user',
            'is_active' => true
        ]);
        
        Auth::login($user);
        
        return redirect()->route('home')->with('success', 'Account created successfully!');
    }
    
    public function showHospitalRegister()
    {
        return view('auth.hospital-register');
    }
    
    public function hospitalRegister(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:users_table',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'hospital_name' => 'required|string|max:100',
            'hospital_location' => 'required|string',
            'contact_info' => 'required|string',
            'information' => 'required|string',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'province' => 'required|string|max:100',
        ]);
        
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password_hash' => Hash::make($request->password),
            'role' => 'hospital_admin',
            'is_active' => true // Immediately active
        ]);
        
        $hospital = Hospital::create([
            'name' => $request->hospital_name,
            'province' => $request->province,
            'location' => $request->hospital_location,
            'contact_info' => $request->contact_info,
            'information' => $request->information,
            'owner_id' => $user->user_id,
            'status' => 'active' // Immediately visible on front page
        ]);
        
        $hospital->subscriptions()->create([
            'plan_type' => $request->subscription_plan,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addYear()
        ]);
        
        return redirect()->route('login')->with('success', 'Hospital registration submitted! Please wait for admin approval.');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
    
    public function profile()
    {
        return view('profile');
    }
    
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $user = auth()->user();
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone' => $request->phone
        ]);
        
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $user->update(['profile_picture' => $path]);
        }
        
        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = \App\Models\User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'first_name' => $googleUser->user['given_name'] ?? '',
                'last_name' => $googleUser->user['family_name'] ?? '',
                'email' => $googleUser->getEmail(),
                'profile_picture' => $googleUser->getAvatar(),
                'role' => 'user',
                'is_active' => true,
            ]
        );

        \Auth::login($user, true);

        return redirect()->route('home');
    }
}

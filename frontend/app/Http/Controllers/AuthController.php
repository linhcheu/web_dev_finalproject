<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $this->signIn($this->api->post('auth/login', $credentials));

        return auth()->user()->isHospitalAdmin()
            ? redirect()->route('hospital.dashboard')
            : redirect()->intended(route('home'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->signIn($this->api->post('auth/register', $data));

        return redirect()->route('home')->with('success', 'Account created successfully!');
    }

    public function showHospitalRegister(): View
    {
        return view('auth.hospital-register', ['provinces' => HospitalController::PROVINCES]);
    }

    public function hospitalRegister(Request $request): RedirectResponse
    {
        // Only these fields are forwarded. The demo card fields on the form are never sent or stored.
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'hospital_name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'hospital_location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'required|string|max:2000',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
        ]);

        $response = $this->api->post('auth/register-hospital', $data);

        return redirect()->route('login')->with('success', $response['message']);
    }

    public function logout(Request $request): RedirectResponse
    {
        rescue(fn () => $this->api->post('auth/logout'), report: false);

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function signIn(array $response): void
    {
        Auth::guard()->login($response['data']['user'], $response['data']['token']);
    }
}

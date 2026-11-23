<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('admin_login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $data = $this->api->post('admin/auth/login', $credentials)['data'];
        Auth::guard('admin')->login($data['admin'], $data['token']);

        return redirect()->intended(route('dashboard'))->with('success', 'Welcome, Admin!');
    }

    public function logout(Request $request): RedirectResponse
    {
        rescue(fn () => $this->api->post('admin/auth/logout'), report: false);

        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login.form')->with('success', 'Logged out successfully.');
    }
}

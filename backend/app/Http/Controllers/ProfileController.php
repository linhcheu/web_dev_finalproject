<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** The logged-in admin's own profile. */
class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('profile_edit', ['user' => Auth::user(), 'isAdmin' => true]);
    }

    /** The page has one form per "update_type". */
    public function update(Request $request): RedirectResponse
    {
        if ($request->input('update_type') === 'password') {
            $this->api->put('admin/me/password', $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]));

            return redirect()->route('profile.edit')->with('success', 'Password updated successfully!');
        }

        $data = match ($request->input('update_type')) {
            'profile_picture' => $request->validate(['profile_picture' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:2048']),
            'basic_info' => ['username' => $request->validate(['name' => 'required|string|max:100'])['name']],
            'email' => $request->validate(['email' => 'required|email|max:100']),
            default => null,
        };

        if ($data === null) {
            return back()->with('error', 'Invalid update type.');
        }

        $admin = $this->api->post('admin/me', $data, ['username' => 'name'])['data'];
        Auth::guard('admin')->setAccount($admin);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}

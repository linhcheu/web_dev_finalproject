<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** The logged-in patient's own profile. */
class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('user.profile');
    }

    public function update(Request $request): RedirectResponse
    {
        // The page has two forms: account details, and password.
        if ($request->filled('new_password')) {
            return $this->updatePassword($request);
        }

        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ]);

        $user = $this->api->post('me', array_filter($data, fn ($v) => $v !== null))['data'];
        Auth::guard()->setAccount($user);

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }

    public static function passwordRules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }

    private function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate(self::passwordRules());

        $this->api->put('me/password', $data);

        return back()->with('success', 'Password updated successfully.');
    }
}

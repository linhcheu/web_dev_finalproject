<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Admin;

class ProfileController extends Controller
{
    public function edit()
    {
        // Check if user is logged in as admin or regular user
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $isAdmin = true;
        } else {
            $user = Auth::guard('web')->user();
            $isAdmin = false;
        }

        return view('profile_edit', compact('user', 'isAdmin'));
    }

    public function update(Request $request)
    {
        // Check if user is logged in as admin or regular user
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $isAdmin = true;
        } else {
            $user = Auth::guard('web')->user();
            $isAdmin = false;
        }

        if (!$user) {
            return redirect()->route('admin.login.form')->with('error', 'Please login first.');
        }

        $updateType = $request->input('update_type');

        switch ($updateType) {
            case 'profile_picture':
                return $this->updateProfilePicture($request, $user, $isAdmin);
            
            case 'basic_info':
                return $this->updateBasicInfo($request, $user, $isAdmin);
            
            case 'email':
                return $this->updateEmail($request, $user, $isAdmin);
            
            case 'password':
                return $this->updatePassword($request, $user, $isAdmin);
            
            default:
                return redirect()->route('profile.edit')->with('error', 'Invalid update type.');
        }
    }

    private function updateProfilePicture(Request $request, $user, $isAdmin)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk($isAdmin ? 'admin_public' : 'public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', $isAdmin ? 'admin_public' : 'public');

        // Now update the user
        $user->update(['profile_picture' => $profilePicturePath]);

        return redirect()->route('profile.edit')->with('success', 'Profile picture updated successfully!');
    }

    private function updateBasicInfo(Request $request, $user, $isAdmin)
    {
        if ($isAdmin) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $user->update([
                'username' => $request->name,
            ]);
        } else {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:20',
            ]);

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Basic information updated successfully!');
    }

    private function updateEmail(Request $request, $user, $isAdmin)
    {
        $request->validate([
            'email' => 'required|email|unique:' . ($isAdmin ? 'admins_table' : 'users_table') . ',email,' . $user->{$isAdmin ? 'admin_id' : 'user_id'} . ',' . ($isAdmin ? 'admin_id' : 'user_id'),
        ]);

        $user->update(['email' => $request->email]);

        return redirect()->route('profile.edit')->with('success', 'Email address updated successfully!');
    }

    private function updatePassword(Request $request, $user, $isAdmin)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        
        $user->update(['password_hash' => Hash::make($request->new_password)]);

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully!');
    }
} 
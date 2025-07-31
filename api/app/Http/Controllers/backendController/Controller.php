<?php

namespace App\Http\Controllers\backendController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

abstract class Controller
{
    //
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // In a real app, you would update the user model here
        // For now, we'll just return a success message
        
        if ($request->filled('new_password')) {
            // Validate current password (in real app, check against stored hash)
            if ($request->current_password !== 'admin123') {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            
            // Update password logic would go here
        }

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}

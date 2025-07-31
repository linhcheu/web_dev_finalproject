<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AdminAuthController extends Controller
{
    /**
     * Show login form or return form structure for API
     */
    public function showLoginForm(Request $request)
    {
        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'form_fields' => [
                        'email' => [
                            'type' => 'email',
                            'required' => true,
                            'label' => 'Admin Email'
                        ],
                        'password' => [
                            'type' => 'password',
                            'required' => true,
                            'label' => 'Password'
                        ]
                    ],
                    'validation_rules' => [
                        'email' => 'required|email',
                        'password' => 'required'
                    ]
                ]
            ]);
        }
        
        return view('admin_login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password_hash)) {
            // Check if request expects JSON (API call)
            if ($request->expectsJson() || $request->is('api/*')) {
                // For API: Create token and return JSON
                $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;
                
                return response()->json([
                    'success' => true,
                    'message' => 'Admin login successful',
                    'data' => [
                        'admin' => [
                            'id' => $admin->admin_id,
                            'username' => $admin->username,
                            'email' => $admin->email
                        ],
                        'token' => $token,
                        'token_type' => 'Bearer'
                    ]
                ]);
            }
            
            // For web: Traditional login
            Auth::guard('admin')->login($admin);
            return redirect('/dashboard')->with('success', 'Welcome, Admin!');
        }

        // Handle failed login
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => ['email' => ['The provided credentials are incorrect.']]
            ], 401);
        }

        return redirect()->route('admin.login.form')->with('error', 'Invalid admin credentials.');
    }

    /**
     * Handle admin logout
     */
    public function logout(Request $request)
    {
        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            // For API: Revoke tokens
            if ($request->user()) {
                $request->user()->tokens()->delete();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
        }
        
        // For web: Traditional logout
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login.form')->with('success', 'Logged out successfully.');
    }
}

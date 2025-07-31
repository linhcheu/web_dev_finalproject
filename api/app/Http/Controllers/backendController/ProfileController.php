<?php

namespace App\Http\Controllers\backendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the authenticated admin's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        // Get the authenticated admin via token
        $admin = $request->user();
        
        // Check authentication
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => 'Admin authentication required'
            ], 401);
        }
        
        // Log for debugging
        Log::info('Admin accessing profile edit', [
            'admin_id' => $admin->admin_id,
            'token_info' => [
                'has_token' => $request->bearerToken() ? true : false,
                'token_prefix' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
            ]
        ]);
        
        return response()->json([
            'success' => true,
            'data' => [
                'admin' => [
                    'id' => $admin->admin_id,
                    'username' => $admin->username,
                    'email' => $admin->email,
                    'profile_picture' => $admin->profile_picture
                ],
                'form_fields' => [
                    'username' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Username',
                        'value' => $admin->username
                    ],
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email Address',
                        'value' => $admin->email
                    ],
                    'current_password' => [
                        'type' => 'password',
                        'required' => false,
                        'label' => 'Current Password',
                        'depends_on' => 'change_password'
                    ],
                    'new_password' => [
                        'type' => 'password',
                        'required' => false,
                        'label' => 'New Password',
                        'depends_on' => 'change_password'
                    ],
                    'new_password_confirmation' => [
                        'type' => 'password',
                        'required' => false,
                        'label' => 'Confirm New Password',
                        'depends_on' => 'change_password'
                    ],
                    'change_password' => [
                        'type' => 'checkbox',
                        'required' => false,
                        'label' => 'Change Password'
                    ],
                    'profile_picture' => [
                        'type' => 'file',
                        'required' => false,
                        'label' => 'Profile Picture',
                        'accept' => 'image/*'
                    ]
                ],
                'validation_rules' => [
                    'username' => 'required|string|max:100',
                    'email' => 'required|email|max:100',
                    'current_password' => 'required_with:new_password',
                    'new_password' => 'nullable|min:8|confirmed',
                    'profile_picture' => 'nullable|image|max:2048'
                ],
                'submission_url' => '/api/backend/profile'
            ]
        ]);
    }
    
    /**
     * Update the specified admin profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Get the authenticated admin via token
        $admin = $request->user();
        
        // Check authentication
        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
                'error' => 'Admin authentication required'
            ], 401);
        }
        
        // Log the request for debugging
        Log::info('Admin profile update request', [
            'admin_id' => $admin->admin_id,
            'token_info' => [
                'has_token' => $request->bearerToken() ? true : false,
                'token_prefix' => $request->bearerToken() ? substr($request->bearerToken(), 0, 10) . '...' : null
            ],
            'request_data' => $request->except(['current_password', 'new_password', 'new_password_confirmation'])
        ]);
        
        // Validate the request
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:100|unique:admins_table,email,'.$admin->admin_id.',admin_id',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Prepare update data
        $updateData = [];
        
        if ($request->has('username')) {
            $updateData['username'] = $request->username;
        }
        
        if ($request->has('email')) {
            $updateData['email'] = $request->email;
        }
        
        // Handle password change if requested
        if ($request->filled('new_password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $admin->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect',
                    'errors' => ['current_password' => ['The provided password does not match our records.']]
                ], 422);
            }
            
            $updateData['password_hash'] = Hash::make($request->new_password);
        }
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('admin-profiles', 'public');
            $updateData['profile_picture'] = $path;
        }
        
        // Update the admin
        $admin->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'admin' => [
                    'id' => $admin->admin_id,
                    'username' => $admin->username,
                    'email' => $admin->email,
                    'profile_picture' => $admin->profile_picture
                ]
            ]
        ]);
    }
}
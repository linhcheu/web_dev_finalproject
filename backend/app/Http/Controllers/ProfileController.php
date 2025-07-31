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
    public function show(Request $request)
    {
        // Check if user is logged in as admin or regular user
        $user = null;
        $isAdmin = false;
        
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $isAdmin = true;
        } else {
            $user = Auth::guard('web')->user();
            $isAdmin = false;
        }

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            return redirect()->route('admin.login.form')->with('error', 'Please login first.');
        }

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            $userData = [
                'id' => $isAdmin ? $user->admin_id : $user->user_id,
                'username' => $isAdmin ? $user->username : null,
                'first_name' => $isAdmin ? null : $user->first_name,
                'last_name' => $isAdmin ? null : $user->last_name,
                'email' => $user->email,
                'profile_picture' => $user->profile_picture,
                'is_admin' => $isAdmin
            ];

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $userData
                ]
            ]);
        }

        return view('profile_show', compact('user', 'isAdmin'));
    }

    public function edit(Request $request)
    {
        // Check if user is logged in as admin or regular user
        $user = null;
        $isAdmin = false;
        
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $isAdmin = true;
        } else {
            $user = Auth::guard('web')->user();
            $isAdmin = false;
        }

        if (!$user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            return redirect()->route('admin.login.form')->with('error', 'Please login first.');
        }

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            $formFields = [];
            
            if ($isAdmin) {
                $formFields = [
                    'username' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Username'
                    ],
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email'
                    ],
                    'profile_picture' => [
                        'type' => 'file',
                        'required' => false,
                        'label' => 'Profile Picture'
                    ]
                ];
            } else {
                $formFields = [
                    'first_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'First Name'
                    ],
                    'last_name' => [
                        'type' => 'text',
                        'required' => true,
                        'label' => 'Last Name'
                    ],
                    'email' => [
                        'type' => 'email',
                        'required' => true,
                        'label' => 'Email'
                    ],
                    'phone' => [
                        'type' => 'text',
                        'required' => false,
                        'label' => 'Phone'
                    ],
                    'profile_picture' => [
                        'type' => 'file',
                        'required' => false,
                        'label' => 'Profile Picture'
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'is_admin' => $isAdmin,
                    'form_fields' => $formFields
                ]
            ]);
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
        try {
            \Log::info('Profile picture update started', [
                'user_id' => $user->{$isAdmin ? 'admin_id' : 'user_id'},
                'is_admin' => $isAdmin,
                'has_file' => $request->hasFile('profile_picture')
            ]);

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

            // Check if file was uploaded
            if (!$request->hasFile('profile_picture')) {
                \Log::error('No profile picture file uploaded');
                return redirect()->route('profile.edit')->with('error', 'No image file was uploaded.');
            }

            $file = $request->file('profile_picture');
            
            \Log::info('File details', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_valid' => $file->isValid()
            ]);
            
            // Additional validation
            if (!$file->isValid()) {
                \Log::error('Invalid file upload', ['error' => $file->getError()]);
                return redirect()->route('profile.edit')->with('error', 'Invalid file upload: ' . $file->getError());
            }

        // Delete old profile picture if exists
        if ($user->profile_picture) {
                try {
            Storage::disk('public')->delete($user->profile_picture);
                    \Log::info('Old profile picture deleted', ['path' => $user->profile_picture]);
                } catch (\Exception $e) {
                    // Log error but continue
                    \Log::error('Failed to delete old profile picture: ' . $e->getMessage());
                }
        }

        // Store new profile picture
            $profilePicturePath = $file->store('profile-pictures', 'public');

            if (!$profilePicturePath) {
                \Log::error('Failed to store profile picture');
                return redirect()->route('profile.edit')->with('error', 'Failed to store the uploaded image.');
            }

            \Log::info('Profile picture stored successfully', ['path' => $profilePicturePath]);

        // Now update the user
        $user->update(['profile_picture' => $profilePicturePath]);

            \Log::info('Profile picture update completed successfully', [
                'user_id' => $user->{$isAdmin ? 'admin_id' : 'user_id'},
                'new_path' => $profilePicturePath
            ]);

        return redirect()->route('profile.edit')->with('success', 'Profile picture updated successfully!');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Profile picture validation failed', ['errors' => $e->errors()]);
            return redirect()->route('profile.edit')->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Profile picture upload error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('profile.edit')->with('error', 'An error occurred while uploading the image: ' . $e->getMessage());
        }
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
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $users = User::latest()->paginate(10);

        return view('user_management', compact('totalUsers', 'users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $updateType = $request->input('update_type');

        switch ($updateType) {
            case 'profile_picture':
                return $this->updateProfilePicture($request, $user);
            
            case 'basic_info':
                return $this->updateBasicInfo($request, $user);
            
            case 'email':
                return $this->updateEmail($request, $user);
            
            case 'password':
                return $this->updatePassword($request, $user);
            
            default:
                return redirect()->route('user.edit', $user->user_id)->with('error', 'Invalid update type.');
        }
    }

    private function updateProfilePicture(Request $request, $user)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old profile picture if exists
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new profile picture
        $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
        
        $user->update(['profile_picture' => $profilePicturePath]);

        return redirect()->route('user.edit', $user->user_id)->with('success', 'Profile picture updated successfully!');
    }

    private function updateBasicInfo(Request $request, $user)
    {
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

        return redirect()->route('user.edit', $user->user_id)->with('success', 'Basic information updated successfully!');
    }

    private function updateEmail(Request $request, $user)
    {
        $request->validate([
            'email' => 'required|email|unique:users_table,email,' . $user->user_id . ',user_id',
        ]);

        $user->update(['email' => $request->email]);

        return redirect()->route('user.edit', $user->user_id)->with('success', 'Email address updated successfully!');
    }

    private function updatePassword(Request $request, $user)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        
        $user->update(['password_hash' => Hash::make($request->new_password)]);

        return redirect()->route('user.edit', $user->user_id)->with('success', 'Password updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            
            return response()->json(['success' => true, 'message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }
} 
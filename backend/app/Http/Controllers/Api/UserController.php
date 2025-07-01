<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users_table,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'password' => 'sometimes|required|string|min:8',
        ]);

        $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));
        
        if ($request->filled('password')) {
            $user->update(['password_hash' => Hash::make($request->password)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users_table,email,' . $user->user_id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'current_password' => 'sometimes|required_with:new_password',
            'new_password' => 'sometimes|required|string|min:8',
        ]);

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password_hash)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 422);
            }
            
            $user->update(['password_hash' => Hash::make($request->new_password)]);
        }

        $user->update($request->only(['first_name', 'last_name', 'email', 'phone']));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user
        ]);
    }
} 
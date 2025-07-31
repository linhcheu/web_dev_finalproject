<?php

namespace App\Http\Controllers\backendController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\frontendModels\User;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get total users count
        $totalUsers = User::count();
        
        // Initialize query builder
        $query = User::query();
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Handle role filter
        if ($request->has('role') && !empty($request->role)) {
            $query->where('role', $request->role);
        }
        
        // Handle status filter
        if ($request->has('status')) {
            $active = $request->status === 'active';
            $query->where('is_active', $active);
        }
        
        // Set default sorting if not provided
        $sortBy = $request->input('sort_by', 'user_id');
        $sortOrder = $request->input('sort_order', 'asc');
        
        // Apply sorting
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated results
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);
        
        // Get available roles for filtering
        $roles = User::distinct()->pluck('role')->toArray();
        
        return response()->json([
            'success' => true,
            'data' => [
                'totalUsers' => $totalUsers,
                'users' => $users,
                'roles' => $roles,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder
            ]
        ]);
    }

    /**
     * Get user details for editing.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        // Validate request
        $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:100|unique:users_table,email,' . $id . ',user_id',
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|required|in:user,hospital_admin',
            'is_active' => 'sometimes|boolean',
            'password' => 'nullable|string|min:8',
        ]);
        
        // Update user details
        $updateData = $request->only(['first_name', 'last_name', 'email', 'phone', 'role', 'is_active']);
        
        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password_hash'] = Hash::make($request->password);
        }
        
        $user->update($updateData);
        
        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Find the user
            $user = \App\Models\frontendModels\User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }
            
            // Manually delete related records to avoid Eloquent relationship errors
            \DB::table('appointments')->where('user_id', $id)->delete();
            \DB::table('feedback')->where('user_id', $id)->delete();
            
            // Check if this user is a hospital owner and handle accordingly
            $hospital = \DB::table('hospitals')->where('owner_id', $id)->first();
            if ($hospital) {
                // Either reassign hospital or delete it based on your business rules
                // For now, we'll just update the owner_id to null
                \DB::table('hospitals')->where('owner_id', $id)->update(['owner_id' => null]);
            }
            
            // Also check and delete any personal tokens if that table exists
            if (\Schema::hasTable('personal_access_tokens')) {
                \DB::table('personal_access_tokens')->where('tokenable_id', $id)
                    ->where('tokenable_type', 'App\Models\frontendModels\User')
                    ->delete();
            }
            
            // Finally delete the user record directly via DB to bypass Eloquent
            \DB::table('users_table')->where('user_id', $id)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting user', ['user_id' => $id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
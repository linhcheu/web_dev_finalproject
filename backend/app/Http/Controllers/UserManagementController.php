<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        // Filter by ID
        if ($request->filled('user_id')) {
            $query->where('user_id', 'LIKE', '%' . $request->user_id . '%');
        }
        
        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }
        
        // Filter by name
        if ($request->filled('name')) {
            $query->where(function($q) use ($request) {
                $q->where('first_name', 'LIKE', '%' . $request->name . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $request->name . '%');
            });
        }
        
        // Filter by email
        if ($request->filled('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'user_id');
        $sortOrder = $request->get('sort_order', 'asc');
        
        // Validate sort fields
        $allowedSortFields = ['user_id', 'first_name', 'last_name', 'email', 'role', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'user_id';
        }
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }
        
        $query->orderBy($sortBy, $sortOrder);
        
        $totalUsers = User::count();
        $users = $query->paginate(10)->withQueryString();
        
        // Get unique roles for filter dropdown
        $roles = User::distinct()->pluck('role')->filter()->values();

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'users' => $users->items(),
                    'pagination' => [
                        'current_page' => $users->currentPage(),
                        'last_page' => $users->lastPage(),
                        'per_page' => $users->perPage(),
                        'total' => $users->total()
                    ],
                    'filters' => [
                        'roles' => $roles
                    ],
                    'sort' => [
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder
                    ],
                    'total_users' => $totalUsers
                ]
            ]);
        }
        
        return view('user_management', compact('totalUsers', 'users', 'roles', 'sortBy', 'sortOrder'));
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => [
                        'id' => $user->user_id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'role' => $user->role,
                        'profile_picture' => $user->profile_picture,
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at
                    ]
                ]
            ]);
        }

        return view('user_show', compact('user'));
    }

    public function edit(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Check if request expects JSON (API call)
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'form_fields' => [
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
                        'role' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Role',
                            'options' => ['user' => 'User', 'hospital_admin' => 'Hospital Admin']
                        ],
                        'is_active' => [
                            'type' => 'select',
                            'required' => true,
                            'label' => 'Status',
                            'options' => [1 => 'Active', 0 => 'Inactive']
                        ]
                    ]
                ]
            ]);
        }

        return view('user_edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $updateType = $request->input('update_type');

        // Handle session clearing
        if ($request->has('clear_session')) {
            session()->forget('updated_profile_picture');
            return response()->json(['success' => true]);
        }

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

    public function create()
    {
        // Get roles for the dropdown
        $roles = ['user', 'hospital_admin'];
        
        return view('user_create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users_table,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:user,hospital_admin',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password_hash' => Hash::make($request->password),
            'is_active' => true
        ];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile-pictures', 'public');
            $userData['profile_picture'] = $profilePicturePath;
        }

        $user = User::create($userData);

        return redirect()->route('user.management')
            ->with('success', 'User created successfully!');
    }
}
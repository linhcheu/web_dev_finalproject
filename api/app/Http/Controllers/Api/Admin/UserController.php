<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private const ROLES = [User::ROLE_USER, User::ROLE_HOSPITAL_ADMIN];

    public function index(Request $request): JsonResponse
    {
        [$sortBy, $sortOrder] = $this->sorting(['user_id', 'first_name', 'last_name', 'email', 'role', 'created_at'], 'user_id');

        $users = User::query()
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', (int) $request->user_id))
            ->when(in_array($request->role, self::ROLES, true), fn ($q) => $q->where('role', $request->role))
            ->when($request->filled('name'), fn ($q) => $q->where(fn ($q) => $q
                ->where('first_name', 'like', '%'.$request->name.'%')
                ->orWhere('last_name', 'like', '%'.$request->name.'%')))
            ->when($request->filled('email'), fn ($q) => $q->where('email', 'like', '%'.$request->email.'%'))
            ->when($request->boolean('without_hospital'), fn ($q) => $q->doesntHave('hospital'))
            ->orderBy($sortBy, $sortOrder)
            ->paginate(min((int) $request->input('per_page', 10), 100));

        return $this->paginated($users, ['total_all' => User::count(), 'roles' => self::ROLES]);
    }

    public function show(User $user): JsonResponse
    {
        return $this->ok($user->load('hospital'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users_table,email',
            'phone' => 'nullable|string|max:20',
            'role' => ['required', Rule::in(self::ROLES)],
            'password' => 'required|string|min:8|max:255',
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        $data['password_hash'] = Hash::make($data['password']);
        $data['is_active'] = true;

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'profile-pictures');
        }

        return $this->ok(User::create($data), 'User created successfully!', 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('users_table', 'email')->ignore($user->user_id, 'user_id')],
            'phone' => 'sometimes|nullable|string|max:20',
            'role' => ['sometimes', 'required', Rule::in(self::ROLES)],
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|required|string|min:8|max:255',
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            $user->tokens()->delete();
        }

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'profile-pictures', $user->profile_picture);
        }

        $user->update($data);

        return $this->ok($user->fresh(), 'User updated successfully!');
    }

    public function destroy(User $user): JsonResponse
    {
        ImageStore::delete($user->profile_picture);
        $user->delete();

        return $this->ok(null, 'User deleted successfully.');
    }
}

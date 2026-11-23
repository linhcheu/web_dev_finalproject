<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Platform admin login and own profile.
 */
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password_hash)) {
            throw ValidationException::withMessages(['email' => 'Invalid admin credentials.']);
        }

        return $this->ok([
            'token' => $admin->createToken('admin-panel', ['admin'])->plainTextToken,
            'admin' => $admin,
        ], 'Welcome, Admin!');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Logged out successfully.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->ok($request->user());
    }

    public function update(Request $request): JsonResponse
    {
        $admin = $request->user();

        $data = $request->validate([
            'username' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('admins_table', 'username')->ignore($admin->admin_id, 'admin_id')],
            'email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('admins_table', 'email')->ignore($admin->admin_id, 'admin_id')],
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'profile-pictures', $admin->profile_picture);
        }

        $admin->update($data);

        return $this->ok($admin->fresh(), 'Profile updated successfully!');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:255',
        ]);

        $admin = $request->user();

        if (! Hash::check($data['current_password'], $admin->password_hash)) {
            throw ValidationException::withMessages(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update(['password_hash' => Hash::make($data['new_password'])]);
        $admin->tokens()->where('id', '!=', $admin->currentAccessToken()->id)->delete();

        return $this->ok(null, 'Password updated successfully!');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * The logged-in patient / hospital admin edits their own account.
 */
class ProfileController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('users_table', 'email')->ignore($user->user_id, 'user_id')],
            'phone' => 'sometimes|nullable|string|max:20',
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'profile-pictures', $user->profile_picture);
        }

        $user->update($data);

        return $this->ok($user->fresh(), 'Profile updated successfully!');
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|max:255',
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password_hash)) {
            throw ValidationException::withMessages(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password_hash' => Hash::make($data['new_password'])]);

        // Sign out every other session that used the old password.
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return $this->ok(null, 'Password updated successfully.');
    }
}

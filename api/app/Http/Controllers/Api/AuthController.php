<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Login / registration for patients and hospital administrators.
 */
class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password_hash)) {
            throw ValidationException::withMessages(['email' => 'The provided credentials do not match our records.']);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages(['email' => 'This account is disabled. Please contact support.']);
        }

        return $this->ok($this->issueToken($user), 'Logged in.');
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate($this->accountRules());

        $user = User::create($this->accountAttributes($data, User::ROLE_USER));

        return $this->ok($this->issueToken($user), 'Account created successfully!', 201);
    }

    /**
     * A hospital signs up: creates its administrator account, the hospital (pending
     * until a platform admin approves it) and a one-year subscription.
     */
    public function registerHospital(Request $request): JsonResponse
    {
        $data = $request->validate($this->accountRules() + [
            'hospital_name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'hospital_location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'required|string|max:2000',
            'subscription_plan' => ['required', Rule::in(Subscription::PLANS)],
        ]);

        DB::transaction(function () use ($data) {
            $owner = User::create($this->accountAttributes($data, User::ROLE_HOSPITAL_ADMIN));

            $hospital = Hospital::create([
                'name' => $data['hospital_name'],
                'province' => $data['province'],
                'location' => $data['hospital_location'],
                'contact_info' => $data['contact_info'],
                'information' => $data['information'],
                'owner_id' => $owner->user_id,
                'status' => 'pending',
            ]);

            $hospital->subscriptions()->create([
                'plan_type' => $data['subscription_plan'],
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addYear(),
            ]);
        });

        return $this->ok(null, 'Hospital registration submitted! You can log in once an administrator approves it.', 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'Logged out.');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->ok($request->user());
    }

    private function accountRules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users_table,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|max:255',
        ];
    }

    private function accountAttributes(array $data, string $role): array
    {
        return [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password_hash' => Hash::make($data['password']),
            'role' => $role,
            'is_active' => true,
        ];
    }

    private function issueToken(User $user): array
    {
        return [
            'token' => $user->createToken('web', [$user->role])->plainTextToken,
            'user' => $user,
        ];
    }
}

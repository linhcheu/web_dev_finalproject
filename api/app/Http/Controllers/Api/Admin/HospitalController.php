<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\Subscription;
use App\Models\User;
use App\Support\ImageStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class HospitalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        [$sortBy, $sortOrder] = $this->sorting(['hospital_id', 'name', 'province', 'status', 'created_at'], 'hospital_id');

        $hospitals = Hospital::with('subscription')
            ->when($request->filled('hospital_id'), fn ($q) => $q->where('hospital_id', (int) $request->hospital_id))
            ->when($request->filled('name'), fn ($q) => $q->where('name', 'like', '%'.$request->name.'%'))
            ->when($request->filled('province') && $request->province !== 'all', fn ($q) => $q->where('province', $request->province))
            ->when(in_array($request->status, Hospital::STATUSES, true), fn ($q) => $q->where('status', $request->status))
            ->when($request->subscription_status === 'has_subscription', fn ($q) => $q->has('subscriptions'))
            ->when($request->subscription_status === 'no_subscription', fn ($q) => $q->doesntHave('subscriptions'))
            ->orderBy($sortBy, $sortOrder)
            ->paginate(min((int) $request->input('per_page', 10), 100));

        return $this->paginated($hospitals, [
            'total_all' => Hospital::count(),
            'provinces' => Hospital::whereNotNull('province')->distinct()->orderBy('province')->pluck('province'),
        ]);
    }

    public function show(Hospital $hospital): JsonResponse
    {
        return $this->ok($hospital->load(['subscription', 'owner']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'location' => 'required|string|max:500',
            'contact_info' => 'required|string|max:500',
            'information' => 'nullable|string|max:2000',
            'status' => ['required', Rule::in(Hospital::STATUSES)],
            'owner_id' => [
                'nullable', 'integer',
                Rule::exists('users_table', 'user_id')->where('role', User::ROLE_HOSPITAL_ADMIN),
                Rule::unique('hospitals', 'owner_id'),
            ],
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
            'create_subscription' => 'sometimes|boolean',
            'plan_type' => ['nullable', Rule::in(Subscription::PLANS)],
        ]);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'hospital-profile-pictures');
        }

        $hospital = DB::transaction(function () use ($data, $request) {
            $hospital = Hospital::create($data);

            if ($request->boolean('create_subscription')) {
                $hospital->subscriptions()->create([
                    'plan_type' => $data['plan_type'] ?? 'basic',
                    'status' => 'active',
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                ]);
            }

            return $hospital;
        });

        return $this->ok($hospital->load('subscription'), 'Hospital created successfully!', 201);
    }

    /**
     * Partial update. Send only the fields to change. Fields starting with
     * "owner_" update the hospital's administrator account.
     */
    public function update(Request $request, Hospital $hospital): JsonResponse
    {
        $owner = $hospital->owner;

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'province' => 'sometimes|required|string|max:100',
            'location' => 'sometimes|required|string|max:500',
            'contact_info' => 'sometimes|required|string|max:500',
            'information' => 'sometimes|nullable|string|max:2000',
            'status' => ['sometimes', 'required', Rule::in(Hospital::STATUSES)],
            'profile_picture' => 'sometimes|'.ImageStore::RULE,
            'owner_first_name' => 'sometimes|required|string|max:100',
            'owner_last_name' => 'sometimes|required|string|max:100',
            'owner_email' => ['sometimes', 'required', 'email', 'max:100', Rule::unique('users_table', 'email')->ignore($owner?->user_id, 'user_id')],
            'owner_password' => 'sometimes|required|string|min:8|max:255',
            'owner_profile_picture' => 'sometimes|'.ImageStore::RULE,
        ]);

        $ownerData = [];
        foreach (['first_name', 'last_name', 'email'] as $field) {
            if (array_key_exists("owner_$field", $data)) {
                $ownerData[$field] = $data["owner_$field"];
            }
        }
        if (isset($data['owner_password'])) {
            $ownerData['password_hash'] = Hash::make($data['owner_password']);
        }

        if (($ownerData || $request->hasFile('owner_profile_picture')) && ! $owner) {
            return response()->json(['message' => 'This hospital has no administrator account.'], 422);
        }

        if ($request->hasFile('owner_profile_picture')) {
            $ownerData['profile_picture'] = ImageStore::replace($request->file('owner_profile_picture'), 'profile-pictures', $owner->profile_picture);
        }

        $hospitalData = array_intersect_key($data, array_flip($hospital->getFillable()));
        if ($request->hasFile('profile_picture')) {
            $hospitalData['profile_picture'] = ImageStore::replace($request->file('profile_picture'), 'hospital-profile-pictures', $hospital->profile_picture);
        }

        DB::transaction(function () use ($hospital, $hospitalData, $owner, $ownerData) {
            $hospital->update($hospitalData);
            if ($ownerData) {
                $owner->update($ownerData);
            }
        });

        return $this->ok($hospital->fresh(['subscription', 'owner']), 'Hospital updated successfully!');
    }

    public function destroy(Hospital $hospital): JsonResponse
    {
        ImageStore::delete($hospital->profile_picture);
        $hospital->delete();

        return $this->ok(null, 'Hospital deleted successfully.');
    }
}

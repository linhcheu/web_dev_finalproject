<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private function bookableHospital(): Hospital
    {
        $owner = User::factory()->hospitalAdmin()->create();
        $hospital = Hospital::create([
            'name' => 'Test Hospital', 'province' => 'Phnom Penh', 'location' => 'St. 1',
            'owner_id' => $owner->user_id, 'status' => 'active',
        ]);
        $hospital->subscriptions()->create([
            'plan_type' => 'basic', 'status' => 'active', 'start_date' => now(), 'end_date' => now()->addYear(),
        ]);

        return $hospital;
    }

    public function test_public_endpoints_need_no_token(): void
    {
        $this->bookableHospital();

        $this->getJson('/api/v1/home')->assertOk()->assertJsonStructure(['data' => ['popular_hospitals', 'recent_feedback']]);
        $this->getJson('/api/v1/hospitals')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_pending_hospitals_are_hidden_from_the_public(): void
    {
        $hospital = Hospital::create(['name' => 'Pending', 'location' => 'x', 'status' => 'pending']);

        $this->getJson("/api/v1/hospitals/{$hospital->hospital_id}")->assertNotFound();
    }

    public function test_register_and_login_return_a_token_but_never_the_password(): void
    {
        $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Ann', 'last_name' => 'Lee', 'email' => 'ann@example.com',
            'phone' => '012345678', 'password' => 'secret123',
        ])->assertCreated()->assertJsonPath('data.user.role', 'user')->assertJsonMissingPath('data.user.password_hash');

        $this->postJson('/api/v1/auth/login', ['email' => 'ann@example.com', 'password' => 'secret123'])
            ->assertOk()->assertJsonStructure(['data' => ['token', 'user']]);

        $this->postJson('/api/v1/auth/login', ['email' => 'ann@example.com', 'password' => 'wrong'])
            ->assertUnprocessable();
    }

    public function test_hospital_registration_waits_for_approval(): void
    {
        $this->postJson('/api/v1/auth/register-hospital', [
            'first_name' => 'Bo', 'last_name' => 'Ra', 'email' => 'bo@example.com', 'phone' => '012345678',
            'password' => 'secret123', 'hospital_name' => 'New Clinic', 'province' => 'Kep',
            'hospital_location' => 'St. 2', 'contact_info' => '012', 'information' => 'Info',
            'subscription_plan' => 'premium',
        ])->assertCreated();

        $this->assertDatabaseHas('hospitals', ['name' => 'New Clinic', 'status' => 'pending']);
    }

    public function test_login_is_rate_limited(): void
    {
        foreach (range(1, 5) as $_) {
            $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'bad'])->assertUnprocessable();
        }
        $this->postJson('/api/v1/auth/login', ['email' => 'x@example.com', 'password' => 'bad'])->assertTooManyRequests();
    }

    public function test_patient_books_and_sees_only_own_appointments(): void
    {
        $hospital = $this->bookableHospital();
        $patient = User::factory()->create();
        $other = User::factory()->create();
        $foreign = $other->appointments()->create([
            'hospital_id' => $hospital->hospital_id, 'appointment_date' => now()->addDay(),
            'appointment_time' => '09:00', 'patient_phone' => '012345678', 'symptom' => 'x',
        ]);

        Sanctum::actingAs($patient);

        $id = $this->postJson('/api/v1/appointments', [
            'hospital_id' => $hospital->hospital_id, 'appointment_date' => now()->addDay()->toDateString(),
            'appointment_time' => '10:30', 'symptom' => 'Headache', 'patient_phone' => '012345678',
        ])->assertCreated()->json('data.appointment_id');

        $this->getJson('/api/v1/appointments')->assertOk()->assertJsonPath('meta.total', 1);
        $this->getJson("/api/v1/appointments/$id")->assertOk();
        $this->getJson("/api/v1/appointments/{$foreign->appointment_id}")->assertNotFound();
    }

    public function test_cannot_book_a_past_date_or_unapproved_hospital(): void
    {
        $pending = Hospital::create(['name' => 'P', 'location' => 'x', 'status' => 'pending']);
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/appointments', [
            'hospital_id' => $pending->hospital_id, 'appointment_date' => now()->subDay()->toDateString(),
            'appointment_time' => '10:30', 'symptom' => 'x', 'patient_phone' => '012345678',
        ])->assertUnprocessable()->assertJsonValidationErrors(['hospital_id', 'appointment_date']);
    }

    public function test_roles_cannot_reach_each_others_endpoints(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->getJson('/api/v1/admin/users')->assertForbidden();
        $this->getJson('/api/v1/my-hospital')->assertForbidden();

        Sanctum::actingAs(User::factory()->hospitalAdmin()->create());
        $this->getJson('/api/v1/appointments')->assertForbidden();

        Sanctum::actingAs(Admin::create(['username' => 'a', 'email' => 'a@x.com', 'password_hash' => Hash::make('x')]));
        $this->getJson('/api/v1/appointments')->assertForbidden();
        $this->getJson('/api/v1/admin/dashboard')->assertOk();

        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/admin/dashboard')->assertUnauthorized();
    }

    public function test_hospital_admin_only_updates_own_hospitals_appointments(): void
    {
        $mine = $this->bookableHospital();
        $theirs = $this->bookableHospital();
        $patient = User::factory()->create();
        $make = fn (Hospital $h) => $patient->appointments()->create([
            'hospital_id' => $h->hospital_id, 'appointment_date' => now()->addDay(),
            'appointment_time' => '09:00', 'patient_phone' => '012345678', 'symptom' => 'x',
        ]);
        $ownAppt = $make($mine);
        $foreignAppt = $make($theirs);

        Sanctum::actingAs($mine->owner);

        $this->patchJson("/api/v1/my-hospital/appointments/{$ownAppt->appointment_id}", ['status' => 'completed'])->assertOk();
        $this->patchJson("/api/v1/my-hospital/appointments/{$foreignAppt->appointment_id}", ['status' => 'completed'])->assertNotFound();
        $this->assertSame('upcoming', $foreignAppt->fresh()->status);
    }

    public function test_profile_picture_upload_rejects_non_images(): void
    {
        Storage::fake('public');
        Sanctum::actingAs($user = User::factory()->create());

        $this->post('/api/v1/me', ['profile_picture' => UploadedFile::fake()->create('evil.php', 10, 'application/x-php')], ['Accept' => 'application/json'])
            ->assertUnprocessable();

        $path = $this->post('/api/v1/me', ['profile_picture' => UploadedFile::fake()->image('me.png')], ['Accept' => 'application/json'])
            ->assertOk()->json('data.profile_picture');
        Storage::disk('public')->assertExists($path);
    }

    public function test_admin_deleting_a_hospital_removes_its_owner_and_appointments(): void
    {
        $hospital = $this->bookableHospital();
        User::factory()->create()->appointments()->create([
            'hospital_id' => $hospital->hospital_id, 'appointment_date' => now()->addDay(),
            'appointment_time' => '09:00', 'patient_phone' => '012345678', 'symptom' => 'x',
        ]);

        Sanctum::actingAs(Admin::create(['username' => 'a', 'email' => 'a@x.com', 'password_hash' => Hash::make('x')]));
        $this->deleteJson("/api/v1/admin/hospitals/{$hospital->hospital_id}")->assertOk();

        $this->assertDatabaseMissing('users_table', ['user_id' => $hospital->owner_id]);
        $this->assertSame(0, Appointment::count());
    }
}

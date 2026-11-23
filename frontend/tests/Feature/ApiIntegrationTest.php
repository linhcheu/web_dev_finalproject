<?php

namespace Tests\Feature;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The website talks only to the API. These tests fake the API's answers.
 */
class ApiIntegrationTest extends TestCase
{
    private array $patient = [
        'user_id' => 7, 'first_name' => 'Ann', 'last_name' => 'Lee', 'email' => 'ann@example.com',
        'phone' => '012345678', 'role' => 'user', 'profile_picture' => null, 'is_active' => true,
    ];

    private function loginAsPatient(): void
    {
        $this->withSession([
            'api_auth.web.token' => 'test-token',
            'api_auth.web.account' => $this->patient,
        ]);
    }

    public function test_home_page_shows_hospitals_from_the_api(): void
    {
        Http::fake(['*/api/v1/home' => Http::response(['data' => [
            'popular_hospitals' => [['hospital_id' => 1, 'name' => 'Calmette Hospital', 'province' => 'Phnom Penh', 'location' => 'St. 1', 'status' => 'active', 'subscription' => null]],
            'recent_feedback' => [],
        ]])]);

        $this->get('/')->assertOk()->assertSee('Calmette Hospital');
    }

    public function test_login_keeps_the_api_token_in_the_session(): void
    {
        Http::fake(['*/api/v1/auth/login' => Http::response(['data' => ['token' => 'abc', 'user' => $this->patient]])]);

        $this->post('/login', ['email' => 'ann@example.com', 'password' => 'secret123'])
            ->assertRedirect(route('home'))
            ->assertSessionHas('api_auth.web.token', 'abc');

        Http::assertSent(fn ($request) => $request['email'] === 'ann@example.com');
    }

    public function test_api_validation_errors_come_back_to_the_form(): void
    {
        Http::fake(['*/api/v1/auth/login' => Http::response([
            'message' => 'Invalid', 'errors' => ['email' => ['The provided credentials do not match our records.']],
        ], 422)]);

        $this->from('/login')->post('/login', ['email' => 'ann@example.com', 'password' => 'wrong'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');
    }

    public function test_requests_send_the_bearer_token(): void
    {
        $this->loginAsPatient();
        Http::fake(['*/api/v1/appointments*' => Http::response([
            'data' => [], 'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 10, 'total' => 0],
        ])]);

        $this->get('/appointments')->assertOk();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer test-token'));
    }

    public function test_expired_token_logs_the_user_out(): void
    {
        $this->loginAsPatient();
        Http::fake(['*' => Http::response(['message' => 'Unauthenticated.'], 401)]);

        $this->get('/appointments')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guests_and_wrong_roles_are_redirected(): void
    {
        $this->get('/appointments')->assertRedirect(route('login'));

        $this->loginAsPatient();
        $this->get('/hospital/dashboard')->assertRedirect(route('home'));
    }

    public function test_api_down_shows_a_friendly_error(): void
    {
        Http::fake(fn () => throw new ConnectionException('down'));

        $this->get('/')->assertStatus(503);
    }

    public function test_security_headers_are_sent(): void
    {
        Http::fake(['*' => Http::response(['data' => ['popular_hospitals' => [], 'recent_feedback' => []]])]);

        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The admin panel talks only to the API. These tests fake the API's answers.
 */
class AdminPanelTest extends TestCase
{
    private array $admin = ['admin_id' => 1, 'username' => 'admin', 'email' => 'admin@careconnect.com', 'profile_picture' => null];

    private function loginAsAdmin(): void
    {
        $this->withSession(['api_auth.admin.token' => 'admin-token', 'api_auth.admin.account' => $this->admin]);
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect(route('admin.login.form'));
    }

    public function test_admin_login_uses_the_api(): void
    {
        Http::fake(['*/api/v1/admin/auth/login' => Http::response(['data' => ['token' => 'admin-token', 'admin' => $this->admin]])]);

        $this->post('/admin/login', ['email' => 'admin@careconnect.com', 'password' => 'admin123'])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('api_auth.admin.token', 'admin-token');
    }

    public function test_dashboard_shows_api_statistics(): void
    {
        $this->loginAsAdmin();
        Http::fake(['*/api/v1/admin/dashboard' => Http::response(['data' => [
            'stats' => ['total_users' => 321, 'total_appointments' => 54, 'total_hospitals' => 7, 'total_feedback' => 9],
            'recent_users' => [], 'recent_appointments' => [], 'recent_hospitals' => [],
        ]])]);

        $this->get('/dashboard')->assertOk()->assertSee('321');

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer admin-token'));
    }

    public function test_delete_returns_json_for_the_page_script(): void
    {
        $this->loginAsAdmin();
        Http::fake(['*/api/v1/admin/users/5' => Http::response(['message' => 'User deleted successfully.'])]);

        $this->deleteJson('/user_management/5')->assertOk()->assertJson(['success' => true]);
    }
}

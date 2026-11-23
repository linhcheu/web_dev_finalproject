<?php

namespace App\Services\Api;

use App\Models\ApiModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Session\Session;

/**
 * Keeps the logged-in account and its API token in the session.
 * auth()->user(), auth()->check(), @auth and the "auth"/"guest" middleware all work as usual.
 */
class ApiSessionGuard implements Guard
{
    private ?Authenticatable $user = null;

    /**
     * @param  class-string<ApiModel>  $model
     */
    public function __construct(
        private readonly string $name,
        private readonly string $model,
        private readonly Session $session,
    ) {}

    public function login(array $account, string $token): void
    {
        $this->session->migrate(true); // new session id: prevents session fixation
        $this->session->put($this->key('token'), $token);
        $this->setAccount($account);
    }

    /** Replace the stored account details, e.g. after a profile update. */
    public function setAccount(array $account): void
    {
        $this->session->put($this->key('account'), $account);
        $this->user = null;
    }

    public function logout(): void
    {
        $this->session->forget([$this->key('token'), $this->key('account')]);
        $this->user = null;
    }

    public function token(): ?string
    {
        return $this->session->get($this->key('token'));
    }

    public function user(): ?Authenticatable
    {
        if ($this->user === null && ($account = $this->session->get($this->key('account')))) {
            $this->user = $this->model::fromApi($account);
        }

        return $this->user;
    }

    public function check(): bool
    {
        return $this->token() !== null && $this->user() !== null;
    }

    public function guest(): bool
    {
        return ! $this->check();
    }

    public function id(): mixed
    {
        return $this->user()?->getAuthIdentifier();
    }

    public function validate(array $credentials = []): bool
    {
        return false; // credentials are checked by the API
    }

    public function hasUser(): bool
    {
        return $this->user() !== null;
    }

    public function setUser(Authenticatable $user): static
    {
        $this->user = $user;

        return $this;
    }

    private function key(string $suffix): string
    {
        return "api_auth.{$this->name}.$suffix";
    }
}

<?php

namespace App\Services\Api;

use App\Models\ApiModel;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Talks to the CareConnect API (api/ app). This web app has no database of its
 * own: every read and write goes through here.
 *
 * API errors are turned into normal Laravel behaviour:
 *   422 -> ValidationException (redirect back with $errors, like $request->validate())
 *   401 -> logged out, redirected to the login page
 *   403 / 404 -> the matching error page
 */
class ApiClient
{
    public function get(string $path, array $query = []): array
    {
        return $this->send('get', $path, $query);
    }

    /** Sends a form; any UploadedFile in $data is sent as a multipart upload. */
    public function post(string $path, array $data = [], array $errorKeys = []): array
    {
        return $this->send('post', $path, $data, $errorKeys);
    }

    public function put(string $path, array $data = [], array $errorKeys = []): array
    {
        return $this->send('put', $path, $data, $errorKeys);
    }

    public function patch(string $path, array $data = [], array $errorKeys = []): array
    {
        return $this->send('patch', $path, $data, $errorKeys);
    }

    public function delete(string $path): array
    {
        return $this->send('delete', $path);
    }

    /**
     * GET a paginated list and turn it into a Laravel paginator of models,
     * so Blade's ->links(), ->hasPages() etc. keep working.
     *
     * @param  class-string<ApiModel>  $model
     */
    public function paginate(string $path, string $model, array $query = []): ApiPaginator
    {
        $body = $this->get($path, $query);
        $meta = $body['meta'] ?? [];

        $paginator = new ApiPaginator(
            $model::collectionFromApi($body['data'] ?? []),
            $meta['total'] ?? 0,
            $meta['per_page'] ?? 10,
            $meta['current_page'] ?? 1,
            ['path' => request()->url(), 'query' => request()->query()],
        );

        // Extra keys the API sent alongside the list (filters, totals, ...).
        $paginator->extra = collect($body)->except(['data', 'meta'])->all();

        return $paginator;
    }

    private function send(string $method, string $path, array $data = [], array $errorKeys = []): array
    {
        $request = $this->request();
        $files = array_filter($data, fn ($value) => $value instanceof UploadedFile);

        if ($files) {
            foreach ($files as $name => $file) {
                $request->attach($name, $file->get(), $file->getClientOriginalName());
            }
            $data = array_diff_key($data, $files);
            $data = array_map(fn ($v) => is_bool($v) ? (int) $v : $v, array_filter($data, fn ($v) => $v !== null));
        }

        try {
            $response = $method === 'get'
                ? $request->get($path, $data)
                : $request->{$files ? 'post' : $method}($path, $data);
        } catch (ConnectionException $e) {
            report($e);
            abort(503, 'The CareConnect service is unavailable. Please try again in a moment.');
        }

        return $this->handle($response, $errorKeys);
    }

    private function request(): PendingRequest
    {
        $request = Http::baseUrl(rtrim(config('services.api.url'), '/').'/api/v1')
            ->acceptJson()
            ->timeout(config('services.api.timeout', 15));

        $token = Auth::guard()->token();

        return $token ? $request->withToken($token) : $request;
    }

    private function handle(Response $response, array $errorKeys): array
    {
        if ($response->successful()) {
            return $response->json() ?? [];
        }

        switch ($response->status()) {
            case 401:
                Auth::guard()->logout();
                throw new AuthenticationException('Your session has expired. Please log in again.');
            case 422:
                // Rename API field names to this form's field names where they differ.
                $errors = collect($response->json('errors', ['form' => [$response->json('message')]]))
                    ->mapWithKeys(fn ($messages, $field) => [$errorKeys[$field] ?? $field => $messages]);
                throw ValidationException::withMessages($errors->all());
            case 429:
                throw ValidationException::withMessages(['email' => 'Too many attempts. Please wait a minute and try again.']);
            case 403:
            case 404:
                abort($response->status(), $response->json('message') ?: '');
            default:
                report(new \RuntimeException("API {$response->status()}: ".$response->body()));
                abort(502, 'Something went wrong. Please try again.');
        }
    }
}

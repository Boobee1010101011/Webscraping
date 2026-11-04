<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Talks to the Face ID server (server-to-server). The API key never reaches the browser.
 * Users are matched to face accounts by their user ID (the primary key), which never changes, unlike an email.
 */
class FaceIdService
{
    public function enabled(): bool
    {
        return filled(config('services.face.api')) && filled(config('services.face.key')) && filled(config('services.face.client'));
    }

    /** Public address of the Face ID scan pages (shown to the browser). */
    public function clientUrl(): string
    {
        return rtrim((string) config('services.face.client'), '/');
    }

    public function hasFace(User $user): bool
    {
        $response = $this->call(fn (PendingRequest $http) => $http->get('/api/integration/users/status', [
            'user_id' => (string) $user->getKey(),
        ]));

        return (bool) $response->json('has_face');
    }

    /** One-time link to the scan page that registers (or replaces) this user's face. */
    public function setupUrl(User $user): string
    {
        $response = $this->call(fn (PendingRequest $http) => $http->post('/api/integration/enroll-tokens', [
            'user_id' => (string) $user->getKey(),
            'name' => $user->name,
        ]));

        $url = $response->json('url');

        if (! is_string($url) || $url === '') {
            throw new RuntimeException('The Face ID server did not return a setup link.');
        }

        return $url;
    }

    /** Removes this user's stored face (call when their account is deleted). */
    public function forget(User $user): void
    {
        $this->call(fn (PendingRequest $http) => $http->delete('/api/integration/users', [
            'user_id' => (string) $user->getKey(),
        ]));
    }

    /** Trades the one-time code from a face scan for the user's ID; null when it is invalid or expired. */
    public function userIdForLoginCode(string $code): ?string
    {
        try {
            $response = $this->call(fn (PendingRequest $http) => $http->post('/api/integration/login-codes/exchange', [
                'code' => $code,
            ]));
        } catch (RequestException) {
            return null;
        }

        $userId = $response->json('user_id');

        return is_string($userId) && $userId !== '' ? $userId : null;
    }

    /**
     * @param  callable(PendingRequest): Response  $request
     *
     * @throws RuntimeException when the Face ID server is off or unreachable
     * @throws RequestException when it answers with an error (4xx/5xx)
     */
    private function call(callable $request)
    {
        if (! $this->enabled()) {
            throw new RuntimeException('Face ID is not configured.');
        }

        $http = Http::baseUrl(rtrim((string) config('services.face.api'), '/'))
            ->withHeaders(['X-API-Key' => (string) config('services.face.key')])
            ->acceptJson()
            ->timeout(8);

        try {
            return $request($http)->throw();
        } catch (ConnectionException $e) {
            throw new RuntimeException('The Face ID server is unreachable.', 0, $e);
        }
    }
}

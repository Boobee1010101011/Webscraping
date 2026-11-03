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
 * Users are matched to face accounts by email.
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
            'email' => mb_strtolower($user->email),
        ]));

        return (bool) $response->json('has_face');
    }

    /** One-time link to the scan page that registers (or replaces) this user's face. */
    public function setupUrl(User $user): string
    {
        $response = $this->call(fn (PendingRequest $http) => $http->post('/api/integration/enroll-tokens', [
            'email' => mb_strtolower($user->email),
            'name' => $user->name,
        ]));

        $url = $response->json('url');

        if (! is_string($url) || $url === '') {
            throw new RuntimeException('The Face ID server did not return a setup link.');
        }

        return $url;
    }

    /** Trades the one-time code from a face scan for the person's email; null when it is invalid or expired. */
    public function emailForLoginCode(string $code): ?string
    {
        try {
            $response = $this->call(fn (PendingRequest $http) => $http->post('/api/integration/login-codes/exchange', [
                'code' => $code,
            ]));
        } catch (RequestException) {
            return null;
        }

        $email = $response->json('email');

        return is_string($email) && $email !== '' ? $email : null;
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

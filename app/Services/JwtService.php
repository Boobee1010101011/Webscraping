<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class JwtService
{
    /** Name of the HttpOnly cookie that carries the token for browser pages. */
    public const COOKIE = 'ppi_token';

    /** @param  string  $method  how the user proved who they are: password or face */
    public function issue(User $user, string $method = 'password'): string
    {
        $now = time();

        return JWT::encode([
            'iss' => config('app.url'),
            'sub' => (string) $user->getKey(),
            'amr' => [$method],
            'jti' => (string) Str::uuid(),
            'iat' => $now,
            'exp' => $now + $this->ttl(),
        ], $this->secret(), 'HS256');
    }

    /** The token's claims, or null when it is malformed, forged or expired. */
    public function parse(string $token): ?object
    {
        $secret = $this->secret();

        try {
            JWT::$leeway = 5;

            return JWT::decode($token, new Key($secret, 'HS256'));
        } catch (Throwable) {
            return null;
        }
    }

    /** Token lifetime in seconds. */
    public function ttl(): int
    {
        return max(1, (int) config('jwt.ttl')) * 60;
    }

    private function secret(): string
    {
        $secret = config('jwt.secret');

        if (! is_string($secret) || $secret === '') {
            throw new RuntimeException('No JWT secret: set JWT_SECRET (or APP_KEY) in .env.');
        }

        return $secret;
    }
}

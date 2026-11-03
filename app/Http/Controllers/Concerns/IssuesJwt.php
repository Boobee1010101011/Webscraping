<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;

trait IssuesJwt
{
    /** Signs the user in: token in the body for API clients, plus an HttpOnly cookie for browser pages. */
    protected function tokenResponse(User $user, string $method): JsonResponse
    {
        $jwt = app(JwtService::class);
        $token = $jwt->issue($user, $method);

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $jwt->ttl(),
            'user' => $this->userPayload($user),
        ])->cookie(
            JwtService::COOKIE, $token, $jwt->ttl() / 60, '/', null, request()->isSecure(), true, false, 'lax',
        );
    }

    /** @return array{id: int, name: string, email: string} */
    protected function userPayload(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email];
    }
}

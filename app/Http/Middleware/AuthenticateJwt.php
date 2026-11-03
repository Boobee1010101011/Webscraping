<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Accepts a JWT from `Authorization: Bearer` (API clients) or the HttpOnly cookie (browser pages).
 * Guests get 401 JSON on the API and a redirect to the login page everywhere else.
 */
class AuthenticateJwt
{
    public function __construct(private JwtService $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?: $request->cookie(JwtService::COOKIE);
        $claims = is_string($token) && $token !== '' ? $this->jwt->parse($token) : null;
        $user = $claims ? User::find($claims->sub) : null;

        if (! $user) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login');
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}

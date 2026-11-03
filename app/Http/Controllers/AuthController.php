<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\IssuesJwt;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\FaceIdService;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use IssuesJwt;

    public function showLogin(Request $request, JwtService $jwt, FaceIdService $face)
    {
        $token = $request->cookie(JwtService::COOKIE);

        if (is_string($token) && $jwt->parse($token)) {
            return redirect()->route('scrape-report');
        }

        return view('login', [
            'faceClient' => $face->enabled() ? $face->clientUrl() : null,
            'registration' => (bool) config('auth.registration'),
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        if (! config('auth.registration')) {
            return response()->json(['message' => 'Sign-up is turned off. Ask an administrator for an account.'], 403);
        }

        $user = User::create($request->only('name', 'email', 'password'));

        return $this->tokenResponse($user, 'password')->setStatusCode(201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::whereRaw('lower(email) = ?', [mb_strtolower(trim($request->input('email')))])->first();

        // Hash::check also runs for unknown emails so the response time doesn't reveal which accounts exist.
        $valid = Hash::check($request->input('password'), $user?->password ?? Hash::make('not-a-password'));

        if (! $user || ! $valid) {
            return response()->json(['message' => 'Wrong email or password.'], 401);
        }

        return $this->tokenResponse($user, 'password');
    }

    public function logout(): JsonResponse
    {
        // Tokens are stateless, so signing out removes the browser cookie; API clients just discard their token.
        return response()->json(null, 204)->withoutCookie(JwtService::COOKIE);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userPayload($request->user())]);
    }
}

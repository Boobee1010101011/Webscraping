<?php

use App\Http\Middleware\AuthenticateJwt;
use App\Services\JwtService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias(['auth.jwt' => AuthenticateJwt::class]);

        // The JWT is signed, so the cookie is sent as-is. Encrypting it would hide it from the API routes.
        $middleware->encryptCookies(except: [JwtService::COOKIE]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Public sign-up
    |--------------------------------------------------------------------------
    |
    | When on, anyone who can reach the login page can create an account and see
    | the dashboard. It defaults to on only for local development: set
    | REGISTRATION_ENABLED=false (or leave APP_ENV non-local) when you deploy.
    |
    */

    'registration' => filter_var(
        env('REGISTRATION_ENABLED', env('APP_ENV', 'production') === 'local'),
        FILTER_VALIDATE_BOOLEAN,
    ),

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', User::class),
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];

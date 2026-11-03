<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JWT signing secret
    |--------------------------------------------------------------------------
    |
    | Set JWT_SECRET in .env. When it is empty the secret is derived from
    | APP_KEY, so a configured application works without extra setup. If both
    | are empty there is no secret and tokens cannot be issued or verified.
    |
    */

    'secret' => env('JWT_SECRET') ?: (env('APP_KEY') ? hash('sha256', 'jwt|'.env('APP_KEY')) : null),

    // Lifetime of a token, in minutes.
    'ttl' => (int) env('JWT_TTL_MINUTES', 60),

];

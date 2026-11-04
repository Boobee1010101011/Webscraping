# Repository Guidelines

## Project Structure & Module Organization

This is a Laravel 12 application. Application code lives in `app/`, routes in `routes/`, and migrations, factories, and seeders in `database/`. Blade templates, JavaScript, and CSS are under `resources/`; the public entry point and static files are in `public/`. Tests are split into `tests/Feature` and `tests/Unit`. Runtime files belong in `storage/`; do not commit generated logs, cache, or session data.

## Build, Test, and Development Commands

- `composer install` / `npm install` - install PHP and frontend dependencies.
- `composer setup` - initialize a checkout, generate the app key, migrate, and build assets.
- `composer dev` - run the Laravel server, queue listener, log viewer, and Vite together.
- `php artisan test` or `composer test` - run Pest tests with in-memory SQLite.
- `npm run dev` / `npm run build` - serve frontend assets with hot reload or build them for production.
- `vendor/bin/pint` - format PHP files with Laravel Pint.

## Coding Style & Naming Conventions

Use four spaces, UTF-8, LF line endings, and a final newline, as defined in `.editorconfig`. Follow Laravel/PHP conventions: `PascalCase` classes, `camelCase` methods and variables, and descriptive migration names. Keep controllers thin; put validation in form requests. Use kebab-case Blade component filenames and clear route names. Run Pint after PHP changes.

## Authentication

- Login is JWT (HS256, `firebase/php-jwt`). `POST /api/auth/login` returns a token and also sets an HttpOnly `ppi_token` cookie (SameSite=Lax) for the Blade pages; API clients may send `Authorization: Bearer` instead. Tokens live `JWT_TTL_MINUTES` (default 60), are stateless, and sign-out only clears the cookie.
- Every dashboard page and `/api/*` route except login sits behind the `auth.jwt` middleware (`app/Http/Middleware/AuthenticateJwt.php`). Guests get a redirect to `/login` on pages and a 401 JSON on the API. New routes belong inside that group.
- The login page has a sign-up form (`POST /api/auth/register`) controlled by `REGISTRATION_ENABLED` (on by default only when `APP_ENV=local`; turn it off when deploying, since anyone who can reach the page could otherwise create an account). Accounts can also be created with `php artisan user:create <email> --name="Name"` (asks for the password; it writes to the database).
- Face ID is optional and talks to the separate Face ID server through `app/Services/FaceIdService.php` (`FACE_API_URL`, `FACE_CLIENT_URL`, `FACE_API_KEY`); users are matched by their user ID (`users.id`), not email, so changing an email never detaches a face. The key stays server-side. The scan pages open in a popup (`resources/views/partials/face-frame.blade.php`). The Face ID server must list this app's origin in its `INTEGRATION_REDIRECTS`, and this app must not run on port 8001, which the Face ID server uses (the default `php artisan serve` port 8000 is fine).
- Feature tests sign in with the `jwtHeaders()` helper in `tests/Pest.php`.

## Testing Guidelines

Add feature tests for routes, authentication, and end-to-end behavior; use unit tests for isolated logic. Name tests descriptively, for example `it_allows_a_user_to_update_their_profile`. Keep tests deterministic and compatible with in-memory SQLite. Run the relevant test file during development, then `composer test` before submitting.

## Commit & Pull Request Guidelines

Existing history is minimal (`init`, `first commit`), so use concise imperative subjects such as `Add scrape report endpoint` or `Fix profile validation`. Keep commits focused. Pull requests should explain the behavior change, list validation commands, link the related issue or task, and include screenshots or request/response examples for UI or API changes. Call out migrations and environment changes.

## Security & Configuration Tips

Copy `.env.example` to `.env` locally and never commit secrets or modify tracked environment credentials. Review authentication, authorization, and request validation when changing routes or controllers. Run migrations deliberately and verify schema changes before deploying.

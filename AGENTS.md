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

## Testing Guidelines

Add feature tests for routes, authentication, and end-to-end behavior; use unit tests for isolated logic. Name tests descriptively, for example `it_allows_a_user_to_update_their_profile`. Keep tests deterministic and compatible with in-memory SQLite. Run the relevant test file during development, then `composer test` before submitting.

## Commit & Pull Request Guidelines

Existing history is minimal (`init`, `first commit`), so use concise imperative subjects such as `Add scrape report endpoint` or `Fix profile validation`. Keep commits focused. Pull requests should explain the behavior change, list validation commands, link the related issue or task, and include screenshots or request/response examples for UI or API changes. Call out migrations and environment changes.

## Security & Configuration Tips

Copy `.env.example` to `.env` locally and never commit secrets or modify tracked environment credentials. Review authentication, authorization, and request validation when changing routes or controllers. Run migrations deliberately and verify schema changes before deploying.

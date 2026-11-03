<?php

use App\Models\User;
use App\Services\JwtService;
use Firebase\JWT\JWT;

function makeUser(string $email = 'jane@example.com', string $password = 'password123'): User
{
    return User::create(['name' => 'Jane Doe', 'email' => $email, 'password' => $password]);
}

it('signs a user in with email and password and returns a token and cookie', function () {
    makeUser();

    $response = $this->postJson('/api/auth/login', ['email' => 'Jane@Example.com', 'password' => 'password123']);

    $response->assertOk()
        ->assertJsonStructure(['token', 'token_type', 'expires_in', 'user' => ['id', 'name', 'email']])
        ->assertJsonPath('user.email', 'jane@example.com')
        ->assertCookie(JwtService::COOKIE);
    expect($response->json('token'))->not->toBeEmpty();
});

it('rejects a wrong password and an unknown email with the same message', function () {
    makeUser();

    $wrongPassword = $this->postJson('/api/auth/login', ['email' => 'jane@example.com', 'password' => 'nope-nope']);
    $unknownEmail = $this->postJson('/api/auth/login', ['email' => 'ghost@example.com', 'password' => 'password123']);

    $wrongPassword->assertStatus(401);
    $unknownEmail->assertStatus(401);
    expect($wrongPassword->json('message'))->toBe($unknownEmail->json('message'));
});

it('requires an email and a password', function () {
    $this->postJson('/api/auth/login', [])->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
});

it('hashes the stored password', function () {
    $user = makeUser();

    expect($user->password)->not->toBe('password123')->and(Hash::check('password123', $user->password))->toBeTrue();
});

it('blocks API routes without a token', function () {
    $this->getJson('/api/facebook-monitoring')->assertStatus(401);
    $this->postJson('/api/trigger-scan')->assertStatus(401);
    $this->getJson('/api/auth/me')->assertStatus(401);
});

it('accepts a Bearer token on API routes', function () {
    $user = makeUser();

    $this->getJson('/api/auth/me', jwtHeaders($user))
        ->assertOk()
        ->assertJsonPath('user.email', 'jane@example.com');
});

it('rejects an expired token', function () {
    $user = makeUser();
    $expired = JWT::encode(
        ['sub' => (string) $user->id, 'iat' => time() - 7200, 'exp' => time() - 3600],
        config('jwt.secret'),
        'HS256',
    );

    $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$expired])->assertStatus(401);
});

it('rejects a token signed with another secret', function () {
    $user = makeUser();
    $forged = JWT::encode(['sub' => (string) $user->id, 'exp' => time() + 3600], 'some-other-secret-0123456789abcdef', 'HS256');

    $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$forged])->assertStatus(401);
});

it('rejects a token for a user that no longer exists', function () {
    $user = makeUser();
    $headers = jwtHeaders($user);
    $user->delete();

    $this->getJson('/api/auth/me', $headers)->assertStatus(401);
});

it('sends guests from dashboard pages to the login page', function () {
    $this->get('/')->assertRedirect('/login');
    $this->get('/tiktok-monitoring')->assertRedirect('/login');
    $this->get('/bank-monitoring')->assertRedirect('/login');
    $this->get('/generated-content')->assertRedirect('/login');
});

it('lets a signed-in user open dashboard pages with the cookie', function () {
    $user = makeUser();
    $token = app(JwtService::class)->issue($user);

    $this->withUnencryptedCookie(JwtService::COOKIE, $token)->get('/')
        ->assertOk()
        ->assertSee('Jane Doe')
        ->assertSee('Sign out');
});

it('shows the login page to guests and skips it for signed-in users', function () {
    $this->get('/login')->assertOk()->assertSee('Sign in');

    $token = app(JwtService::class)->issue(makeUser());
    $this->withUnencryptedCookie(JwtService::COOKIE, $token)->get('/login')->assertRedirect('/');
});

it('clears the cookie on sign out', function () {
    $user = makeUser();

    $this->postJson('/api/auth/logout', [], jwtHeaders($user))
        ->assertNoContent()
        ->assertCookieExpired(JwtService::COOKIE);
});

it('creates a user from the command line', function () {
    $this->artisan('user:create', ['email' => 'New@Example.com', '--name' => 'New Person'])
        ->expectsQuestion('Password (min 8 characters)', 'password123')
        ->expectsQuestion('Repeat password', 'password123')
        ->expectsOutputToContain('Created new@example.com.')
        ->assertSuccessful();

    expect(User::where('email', 'new@example.com')->exists())->toBeTrue();
});

it('refuses a mismatched or short password when creating a user', function () {
    $this->artisan('user:create', ['email' => 'a@example.com', '--name' => 'A'])
        ->expectsQuestion('Password (min 8 characters)', 'password123')
        ->expectsQuestion('Repeat password', 'different')
        ->assertFailed();

    $this->artisan('user:create', ['email' => 'b@example.com', '--name' => 'B'])
        ->expectsQuestion('Password (min 8 characters)', 'short')
        ->expectsQuestion('Repeat password', 'short')
        ->assertFailed();

    expect(User::count())->toBe(0);
});

it('creates an account from the sign-up form and signs the user in', function () {
    config(['auth.registration' => true]);

    $response = $this->postJson('/api/auth/register', [
        'name' => 'New Person',
        'email' => ' New@Example.com ',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertCreated()
        ->assertJsonPath('user.email', 'new@example.com')
        ->assertCookie(JwtService::COOKIE);

    $user = User::where('email', 'new@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New Person')
        ->and(Hash::check('password123', $user->password))->toBeTrue();

    $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$response->json('token')])->assertOk();
});

it('validates the sign-up form', function () {
    config(['auth.registration' => true]);
    makeUser('taken@example.com');

    $this->postJson('/api/auth/register', [])->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);

    $this->postJson('/api/auth/register', [
        'name' => 'X', 'email' => 'TAKEN@example.com', 'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertStatus(422)->assertJsonValidationErrors('email');

    $this->postJson('/api/auth/register', [
        'name' => 'X', 'email' => 'x@example.com', 'password' => 'short', 'password_confirmation' => 'short',
    ])->assertStatus(422)->assertJsonValidationErrors('password');

    $this->postJson('/api/auth/register', [
        'name' => 'X', 'email' => 'x@example.com', 'password' => 'password123', 'password_confirmation' => 'different1',
    ])->assertStatus(422)->assertJsonValidationErrors('password');

    expect(User::count())->toBe(1);
});

it('refuses sign-up when it is turned off', function () {
    config(['auth.registration' => false]);

    $this->postJson('/api/auth/register', [
        'name' => 'X', 'email' => 'x@example.com', 'password' => 'password123', 'password_confirmation' => 'password123',
    ])->assertStatus(403);

    expect(User::count())->toBe(0);
});

it('shows the sign-up form on the login page only when sign-up is on', function () {
    config(['auth.registration' => true]);
    $this->get('/login')->assertOk()->assertSee('Create account')->assertSee('id="signup-form"', false);

    config(['auth.registration' => false]);
    $this->get('/login')->assertOk()->assertDontSee('id="signup-form"', false);
});

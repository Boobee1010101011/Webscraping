<?php

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

function faceUser(string $email = 'jane@example.com'): User
{
    return User::create(['name' => 'Jane Doe', 'email' => $email, 'password' => 'password123']);
}

function enableFaceId(): void
{
    config([
        'services.face.api' => 'http://face.test',
        'services.face.client' => 'http://localhost:3000',
        'services.face.key' => 'secret-key',
    ]);
}

it('reports Face ID as disabled when it is not configured', function () {
    config(['services.face.api' => null, 'services.face.key' => null, 'services.face.client' => null]);

    $this->getJson('/api/face/status', jwtHeaders())
        ->assertOk()
        ->assertExactJson(['enabled' => false]);
});

it('hides Face ID when the Face ID server is unreachable', function () {
    enableFaceId();
    Http::fake(fn () => throw new ConnectionException('refused'));

    $this->getJson('/api/face/status', jwtHeaders())->assertOk()->assertExactJson(['enabled' => false]);
});

it('tells the signed-in user whether they have a face registered', function () {
    enableFaceId();
    Http::fake(['face.test/*' => Http::response(['exists' => true, 'has_face' => false])]);

    $this->getJson('/api/face/status', jwtHeaders(faceUser()))
        ->assertOk()
        ->assertExactJson(['enabled' => true, 'has_face' => false]);

    Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'secret-key')
        && str_contains($request->url(), '/api/integration/users/status')
        && str_contains($request->url(), 'email=jane%40example.com'));
});

it('requires a signed-in user for face status and setup', function () {
    $this->getJson('/api/face/status')->assertStatus(401);
    $this->postJson('/api/face/setup')->assertStatus(401);
});

it('returns the face setup link for the signed-in user', function () {
    enableFaceId();
    Http::fake(['face.test/*' => Http::response(['token' => 't', 'url' => 'http://localhost:3000/enroll?token=t'])]);

    $this->postJson('/api/face/setup', [], jwtHeaders(faceUser()))
        ->assertOk()
        ->assertJsonPath('url', 'http://localhost:3000/enroll?token=t');

    Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'secret-key')
        && $request['email'] === 'jane@example.com'
        && $request['name'] === 'Jane Doe');
});

it('answers 502 when the setup link cannot be fetched', function () {
    enableFaceId();
    Http::fake(['face.test/*' => Http::response([], 500)]);

    $this->postJson('/api/face/setup', [], jwtHeaders())->assertStatus(502);
});

it('signs in with a face login code', function () {
    enableFaceId();
    faceUser();
    Http::fake(['face.test/*' => Http::response(['email' => 'Jane@Example.com', 'name' => 'Jane Doe'])]);

    $response = $this->postJson('/api/auth/face', ['code' => str_repeat('a', 32)]);

    $response->assertOk()
        ->assertJsonPath('user.email', 'jane@example.com')
        ->assertCookie(JwtService::COOKIE);

    $claims = app(JwtService::class)->parse($response->json('token'));
    expect($claims->amr)->toBe(['face']);

    Http::assertSent(fn ($request) => $request->hasHeader('X-API-Key', 'secret-key')
        && str_contains($request->url(), '/api/integration/login-codes/exchange')
        && $request['code'] === str_repeat('a', 32));
});

it('refuses a face login code the Face ID server rejects', function () {
    enableFaceId();
    faceUser();
    Http::fake(['face.test/*' => Http::response(['detail' => 'expired'], 400)]);

    $this->postJson('/api/auth/face', ['code' => str_repeat('a', 32)])->assertStatus(401);
});

it('refuses a face login when no dashboard account has that email', function () {
    enableFaceId();
    Http::fake(['face.test/*' => Http::response(['email' => 'stranger@example.com'])]);

    $this->postJson('/api/auth/face', ['code' => str_repeat('a', 32)])
        ->assertStatus(401)
        ->assertJsonPath('message', 'No dashboard account matches this face.');
});

it('answers 502 for a face login when the Face ID server is down', function () {
    enableFaceId();
    Http::fake(fn () => throw new ConnectionException('refused'));

    $this->postJson('/api/auth/face', ['code' => str_repeat('a', 32)])->assertStatus(502);
});

it('validates the face login code format', function () {
    $this->postJson('/api/auth/face', ['code' => 'short'])->assertStatus(422)->assertJsonValidationErrors('code');
});

it('shows the Face ID button on the login page only when it is configured', function () {
    $this->get('/login')->assertOk()->assertDontSee('Log in with Face ID');

    enableFaceId();
    $this->get('/login')->assertOk()->assertSee('Log in with Face ID');
});

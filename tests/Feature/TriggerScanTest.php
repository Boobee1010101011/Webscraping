<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('triggers a scan through the n8n webhook', function () {
    Http::fake([
        '*' => Http::response(['queued' => true], 200),
    ]);

    $response = $this->postJson('/api/trigger-scan');

    $response->assertOk()->assertJson(['success' => true]);

    Http::assertSent(function ($request) {
        return $request->method() === 'POST'
            && $request->data()['triggered_by'] === 'user_click'
            && isset($request->data()['timestamp']);
    });
});

it('returns an error when the scan webhook fails', function () {
    Http::fake([
        '*' => Http::response([], 500),
    ]);

    $response = $this->postJson('/api/trigger-scan');

    $response->assertStatus(502)->assertJson(['success' => false]);
});

it('returns a useful error when n8n is unreachable', function () {
    Http::fake(function () {
        throw new ConnectionException('Connection refused');
    });

    $response = $this->postJson('/api/trigger-scan');

    $response->assertStatus(503)->assertJson([
        'success' => false,
        'message' => 'n8n is not reachable. Start n8n or check N8N_SCAN_WEBHOOK in .env.',
    ]);
});

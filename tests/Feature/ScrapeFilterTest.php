<?php

use App\Models\FacebookMonitoring;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters reports by database insertion date', function () {
    FacebookMonitoring::create(['id' => 'old', 'created_at' => '2026-09-15 10:00:00']);
    FacebookMonitoring::create(['id' => 'matching', 'created_at' => '2026-09-16 14:00:00']);

    $response = $this->get('/api/facebook-monitoring?date=2026-09-16');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', 'matching');
});

it('filters reports inserted within a relative time range', function () {
    Carbon::setTestNow('2026-09-18 12:00:00');

    FacebookMonitoring::create(['id' => 'recent', 'created_at' => '2026-09-18 11:30:00']);
    FacebookMonitoring::create(['id' => 'old', 'created_at' => '2026-09-18 10:00:00']);

    $response = $this->get('/api/facebook-monitoring?date_range=1h');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', 'recent');

    Carbon::setTestNow();
});

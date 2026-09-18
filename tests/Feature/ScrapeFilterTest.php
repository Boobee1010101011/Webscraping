<?php

use App\Models\Scrape;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

beforeEach(function () {
    Schema::create('n8n_ai_competitor_monitoring', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->timestamp('timestamp')->nullable();
        $table->string('competitor_name')->nullable();
        $table->string('country')->nullable();
        $table->string('post_type')->nullable();
        $table->string('threat_level')->nullable();
        $table->text('original_text')->nullable();
        $table->text('english_summary')->nullable();
        $table->text('ai_counter_strategy_draft')->nullable();
        $table->text('source_url')->nullable();
        $table->text('image_url')->nullable();
        $table->timestamp('created_at')->nullable();
    });
});

it('filters reports by database insertion date', function () {
    Scrape::create(['id' => 'old', 'created_at' => '2026-09-15 10:00:00']);
    Scrape::create(['id' => 'matching', 'created_at' => '2026-09-16 14:00:00']);

    $response = $this->get('/api/scrapes?date=2026-09-16');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', 'matching');
});

it('filters reports inserted within a relative time range', function () {
    Carbon::setTestNow('2026-09-18 12:00:00');

    Scrape::create(['id' => 'recent', 'created_at' => '2026-09-18 11:30:00']);
    Scrape::create(['id' => 'old', 'created_at' => '2026-09-18 10:00:00']);

    $response = $this->get('/api/scrapes?date_range=1h');

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', 'recent');

    Carbon::setTestNow();
});

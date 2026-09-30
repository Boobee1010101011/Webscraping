<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('n8n_bank_monitoring', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('timestamp')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_type')->nullable();
            $table->text('original_text')->nullable();
            $table->text('english_summary')->nullable();
            $table->text('source_url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('content_hash')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('n8n_tiktok_monitoring', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('timestamp')->nullable();
            $table->string('competitor_name')->nullable();
            $table->string('country')->nullable();
            $table->string('post_type')->nullable();
            $table->string('threat_level')->nullable();
            $table->text('caption')->nullable();
            $table->text('transcript')->nullable();
            $table->text('english_summary')->nullable();
            $table->text('ai_counter_strategy_draft')->nullable();
            $table->text('source_url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('content_hash')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('n8n_facebook_monitoring', function (Blueprint $table) {
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
            $table->string('content_hash')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('n8n_facebook_monitoring');
        Schema::dropIfExists('n8n_tiktok_monitoring');
        Schema::dropIfExists('n8n_bank_monitoring');
    }
};

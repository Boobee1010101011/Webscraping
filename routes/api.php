<?php

use App\Http\Controllers\ScrapeController;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::prefix('scrapes')->name('scrapes.')->group(function () {
    Route::get('/', [ScrapeController::class, 'index'])->name('index'); // Resolves to 'scrapes.index'
    Route::get('/{id}', [ScrapeController::class, 'show'])->name('show');   // Resolves to 'scrapes.show'
});

Route::post('/trigger-scan', function () {
    try {
        $response = Http::timeout(10)->post(config('services.n8n.scan_webhook'), [
            'triggered_by' => 'user_click',
            'timestamp' => now()->toIso8601String(),
        ]);
    } catch (ConnectionException) {
        return response()->json([
            'success' => false,
            'message' => 'n8n is not reachable. Start n8n or check N8N_SCAN_WEBHOOK in .env.',
        ], 503);
    }

    if (! $response->successful()) {
        return response()->json([
            'success' => false,
            'message' => 'The n8n scan webhook returned HTTP '.$response->status().'. Check that the workflow is active.',
        ], 502);
    }

    return response()->json(['success' => true]);
})->name('scan.trigger');

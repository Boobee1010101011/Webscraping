<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankMonitoringController;
use App\Http\Controllers\FacebookMonitoringController;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\TiktokMonitoringController;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('auth.login');
Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('auth.register');
Route::post('/auth/face', [FaceController::class, 'login'])->middleware('throttle:10,1')->name('auth.face');

Route::middleware('auth.jwt')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

    Route::get('/face/status', [FaceController::class, 'status'])->name('face.status');
    Route::post('/face/setup', [FaceController::class, 'setup'])->name('face.setup');

    Route::prefix('facebook-monitoring')->name('facebook-monitoring.')->group(function () {
        Route::get('/', [FacebookMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [FacebookMonitoringController::class, 'show'])->name('show');
    });

    Route::prefix('tiktok-monitoring')->name('tiktok-monitoring.')->group(function () {
        Route::get('/', [TiktokMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [TiktokMonitoringController::class, 'show'])->name('show');
    });

    Route::prefix('bank-monitoring')->name('bank-monitoring.')->group(function () {
        Route::get('/', [BankMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [BankMonitoringController::class, 'show'])->name('show');
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
});

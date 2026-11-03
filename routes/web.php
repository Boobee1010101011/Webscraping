<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankMonitoringController;
use App\Http\Controllers\FacebookMonitoringController;
use App\Http\Controllers\TiktokMonitoringController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

Route::middleware('auth.jwt')->group(function () {
    Route::get('/', [FacebookMonitoringController::class, 'renderReportView'])->name('scrape-report');
    Route::get('/tiktok-monitoring', [TiktokMonitoringController::class, 'renderReportView'])->name('tiktok-report');
    Route::get('/bank-monitoring', [BankMonitoringController::class, 'renderReportView'])->name('bank-monitoring');
    Route::view('/generated-content', 'generated-content')->name('generated-content');
});

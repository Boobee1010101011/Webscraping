<?php

use App\Http\Controllers\ScrapeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ScrapeController::class, 'renderReportView'])->name('scrape-report');

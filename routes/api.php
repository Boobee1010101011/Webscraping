<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScrapeController;

Route::prefix('scrapes')->name('scrapes.')->group(function () {
    Route::get('/', [ScrapeController::class, 'index'])->name('index'); // Resolves to 'scrapes.index'
    Route::get('/{id}', [ScrapeController::class, 'show'])->name('show');   // Resolves to 'scrapes.show'
});

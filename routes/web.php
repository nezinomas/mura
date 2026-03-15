<?php

use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // GET: Shows the HTML form
    Route::get('/compose', [QuoteController::class, 'index'])
        ->name('compose.index');

    // POST: Handles the form submission (with a limit of 5 requests per minute)
    Route::post('/compose', [QuoteController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('compose.store');

    Route::delete('/quotes/{quote}', [QuoteController::class, 'destroy'])
        ->name('quotes.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

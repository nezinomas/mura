<?php

use App\Mail\CorrespondenceLetter;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/rules', 'rules')->name('rules');

// The Correspondence Routes
Route::view('/correspondence', 'correspondence')->name('correspondence.create');
Route::post('/correspondence', function (Request $request) {
    $request->validate([
        'email' => ['nullable', 'email', 'max:255'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    // Send using the new formal Mailable class
    Mail::to(config('mail.from.address'))
        ->send(new CorrespondenceLetter($request->message, $request->email));

    return back()->with('success', 'Your letter has been sent.');
})->name('correspondence.store');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('quotes', QuoteController::class)
        ->only(['create', 'store', 'edit', 'update', 'destroy']);

    Route::post('quotes/{quote}/grab', [QuoteController::class, 'grab'])->name('quotes.grab');
    Route::delete('quotes/{quote}/ungrab', [QuoteController::class, 'ungrab'])->name('quotes.ungrab');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');

require __DIR__.'/auth.php';

Route::get('/{user:name}/feed', [App\Http\Controllers\UserController::class, 'feed'])->name('users.feed');
Route::get('/{user:name}', [App\Http\Controllers\UserController::class, 'show'])->name('users.show');

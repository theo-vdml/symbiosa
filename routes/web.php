<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');

Route::inertia('/agenda', 'Events/Index')->name('agenda');
Route::get('/events/{slug}', function (string $slug) {
    return Inertia::render('Events/Show', [
        'slug' => $slug,
    ]);
})->name('events.show');

Route::inertia('/archives', 'Archives')->name('archives');

Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news');
Route::get('/news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])->name('news.show');

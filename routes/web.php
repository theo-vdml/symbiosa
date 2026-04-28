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

Route::inertia('/news', 'News/Index')->name('news');
Route::get('/news/{slug}', function (string $slug) {
    return Inertia::render('News/Show', [
        'slug' => $slug,
    ]);
})->name('news.show');

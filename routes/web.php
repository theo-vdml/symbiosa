<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');

Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])
    ->name('events.index');

Route::get('/events/{slug}', [\App\Http\Controllers\EventController::class, 'show'])
    ->name('events.show');

Route::inertia('/archives', 'Archives')->name('archives');

Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])
    ->name('news');

Route::get('/news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])
    ->name('news.show');

<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\HomepageController::class, 'index'])
    ->name('home');

Route::get('/events', [\App\Http\Controllers\EventController::class, 'index'])
    ->name('events.index');

Route::get('/events/{slug}', [\App\Http\Controllers\EventController::class, 'show'])
    ->name('events.show');

Route::get('/events/{slug}/ticketing', [\App\Http\Controllers\EventController::class, 'ticketing'])
    ->name('events.ticketing');

Route::inertia('/archives', 'Archives')->name('archives');

Route::inertia('/about', 'About')->name('about');
Route::inertia('/contact', 'Contact')->name('contact');

Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])
    ->name('news.index');

Route::get('/news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])
    ->name('news.show');

Route::post('/events/{event:slug}/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])
    ->name('events.checkout.store');

Route::get('/checkout/{checkout:uuid}', [\App\Http\Controllers\CheckoutController::class, 'show'])
    ->name('checkout.show');

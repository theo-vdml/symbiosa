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

Route::get('/archives', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('archives');

Route::get('/about', \App\Http\Controllers\AboutController::class)->name('about');
Route::get('/contact', \App\Http\Controllers\ContactController::class)->name('contact');

Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])
    ->name('news.index');

Route::get('/news/{slug}', [\App\Http\Controllers\NewsController::class, 'show'])
    ->name('news.show');

Route::get('/legal/{slug}', [\App\Http\Controllers\LegalPageController::class, 'show'])
    ->name('legal.show');

Route::post('/events/{event:slug}/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])
    ->name('events.checkout.store');

Route::get('/checkout/{checkout:uuid}', [\App\Http\Controllers\CheckoutController::class, 'show'])
    ->name('checkout.show');

Route::post('/checkout/{checkout:uuid}/start', [\App\Http\Controllers\CheckoutController::class, 'checkout'])
    ->name('checkout.start');

Route::get('/checkout/{checkout:uuid}/success', [\App\Http\Controllers\CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::get('/checkout/{checkout:uuid}/cancel', [\App\Http\Controllers\CheckoutController::class, 'cancel_payment'])
    ->name('checkout.cancel_payment');

Route::get('/checkin/{token}', [\App\Http\Controllers\CheckinController::class, 'publicShow'])->name('checkin.public');
Route::post('/checkin/{token}/auth', [\App\Http\Controllers\CheckinController::class, 'authenticate'])->name('checkin.authenticate');
Route::post('/checkin/{token}/scan', [\App\Http\Controllers\CheckinController::class, 'publicScan'])->name('checkin.public_scan');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkin/list/{checkinList}', [\App\Http\Controllers\CheckinController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/list/{checkinList}/scan', [\App\Http\Controllers\CheckinController::class, 'scan'])->name('checkin.scan');
});

Route::post('/checkin/tickets/{issuedTicket}/toggle', [\App\Http\Controllers\CheckinController::class, 'toggleCheckin'])->name('checkin.toggle');
Route::get('/checkin/tickets/{issuedTicket}/status', [\App\Http\Controllers\CheckinController::class, 'getTicketStatus'])->name('checkin.status');

Route::post('/webhooks/stripe', [\App\Http\Controllers\StripeWebhookController::class, 'handle']);

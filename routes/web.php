<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LegalPageController;
use App\Http\Controllers\LostTicketsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomepageController::class, 'index'])
    ->name('home');

Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');

Route::get('/events/{slug}', [EventController::class, 'show'])
    ->name('events.show');

Route::get('/events/{slug}/ticketing', [EventController::class, 'ticketing'])
    ->name('events.ticketing');

Route::get('/archives', [ArchiveController::class, 'index'])->name('archives');

Route::get('/about', AboutController::class)->name('about');
Route::get('/contact', ContactController::class)->name('contact');

Route::get('/news', [NewsController::class, 'index'])
    ->name('news.index');

Route::get('/news/{slug}', [NewsController::class, 'show'])
    ->name('news.show');

Route::get('/legal/{slug}', [LegalPageController::class, 'show'])
    ->name('legal.show');

Route::get('/lost-tickets', [LostTicketsController::class, 'show'])->name('lost-tickets.show');
Route::post('/lost-tickets', [LostTicketsController::class, 'send'])->name('lost-tickets.send');

Route::post('/events/{event:slug}/checkout', [CheckoutController::class, 'store'])
    ->name('events.checkout.store');

Route::get('/checkout/{checkout:uuid}', [CheckoutController::class, 'show'])
    ->name('checkout.show');

Route::post('/checkout/{checkout:uuid}/send-verification', [CheckoutController::class, 'sendVerificationEmail'])
    ->name('checkout.send-verification');

Route::post('/checkout/{checkout:uuid}/verify', [CheckoutController::class, 'verifyEmail'])
    ->name('checkout.verify');

Route::post('/checkout/{checkout:uuid}/start', [CheckoutController::class, 'checkout'])
    ->name('checkout.start');

Route::get('/checkout/{checkout:uuid}/success', [CheckoutController::class, 'success'])
    ->name('checkout.success');

Route::get('/checkout/{checkout:uuid}/download', [CheckoutController::class, 'downloadTickets'])
    ->name('checkout.download');

Route::get('/checkout/{checkout:uuid}/cancel', [CheckoutController::class, 'cancel_payment'])
    ->name('checkout.cancel_payment');

Route::get('/checkin/{token}', [CheckinController::class, 'publicShow'])->name('checkin.public');
Route::get('/checkin/{token}/search', [CheckinController::class, 'publicSearch'])->name('checkin.public_search');
Route::post('/checkin/{token}/auth', [CheckinController::class, 'authenticate'])
    ->middleware('throttle:checkin-auth')
    ->name('checkin.authenticate');
Route::post('/checkin/{token}/scan', [CheckinController::class, 'publicScan'])->name('checkin.public_scan');
Route::post('/checkin/{token}/tickets/{issuedTicket}/toggle', [CheckinController::class, 'publicToggleCheckin'])->name('checkin.public_toggle');
Route::get('/checkin/{token}/tickets/{issuedTicket}/status', [CheckinController::class, 'publicTicketStatus'])->name('checkin.public_status');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkin/list/{checkinList}', [CheckinController::class, 'show'])->name('checkin.show');
    Route::get('/checkin/list/{checkinList}/search', [CheckinController::class, 'search'])->name('checkin.search');
    Route::post('/checkin/list/{checkinList}/scan', [CheckinController::class, 'scan'])->name('checkin.scan');
    Route::post('/checkin/list/{checkinList}/tickets/{issuedTicket}/toggle', [CheckinController::class, 'toggleCheckin'])->name('checkin.toggle');
    Route::get('/checkin/list/{checkinList}/tickets/{issuedTicket}/status', [CheckinController::class, 'getTicketStatus'])->name('checkin.status');
});

Route::post('/webhooks/stripe', [StripeWebhookController::class, 'handle']);

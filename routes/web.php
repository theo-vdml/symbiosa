<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/agenda', 'Agenda')->name('agenda');
Route::inertia('/archives', 'Archives')->name('archives');
Route::inertia('/news', 'News')->name('news');
Route::get('/news/{slug}', function (string $slug) {
	return Inertia::render('NewsShow', [
		'slug' => $slug,
	]);
})->name('news.show');

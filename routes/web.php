<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Home')->name('home');
Route::inertia('/agenda', 'Agenda')->name('agenda');
Route::inertia('/archives', 'Archives')->name('archives');
Route::inertia('/news', 'News')->name('news');

<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Explore;
use VulcanPhp\PhpRouter\Route;

// CoPlay Admin Route
Route::form('admin', [Admin::class, 'index']);

// Public Routes
Route::get('/', [Home::class, 'home']);
Route::get('/{type}/genre/{slug}', [Explore::class, 'genre'])->name('genre');
Route::get('/{type}/language/{language}', [Explore::class, 'language'])->name('language');
Route::get('/{type}/country/{country}', [Explore::class, 'country'])->name('country');
Route::get('/{type}/company/{slug}', [Explore::class, 'company'])->name('company');
Route::get('/{type}/network/{slug}', [Explore::class, 'network'])->name('network');
Route::get('/people/{slug}', [Explore::class, 'people'])->name('people');
Route::get('/movie/{slug?}', [Home::class, 'movie']);
Route::get('/tv/{slug?}', [Home::class, 'tv']);
Route::get('/watchlist', [Home::class, 'watchlater']);
Route::get('/api', [Home::class, 'api']);
Route::get('/search', [Home::class, 'search']);
Route::get('/embed/{type}/{id}', [Home::class, 'embed'])->name('embed');

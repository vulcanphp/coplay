<?php

/**
 * This file contains the route definitions for the web application.
 *
 * The routes defined in this file are used to map URLs to controller
 * actions or views. The routes are defined using the "router()" function,
 * which is a facade for the Hyper\Router class.
 */

use Spark\Http\Route;

use App\Http\Controllers\ExploreController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/{type}/genre/{slug}', [ExploreController::class, 'genre'])->name('genre');
Route::get('/{type}/language/{language}', [ExploreController::class, 'language'])->name('language');
Route::get('/{type}/country/{country}', [ExploreController::class, 'country'])->name('country');
Route::get('/{type}/company/{slug}', [ExploreController::class, 'company'])->name('company');
Route::get('/{type}/network/{slug}', [ExploreController::class, 'network'])->name('network');
Route::get('/people/{slug}', [ExploreController::class, 'people'])->name('people');
Route::get('/movie/{slug?}', [HomeController::class, 'movie'])->name('movie');
Route::get('/tv/{slug?}', [HomeController::class, 'tv'])->name('tv');
Route::get('/watchlist', [HomeController::class, 'watchlater'])->name('watchlist');
Route::get('/api', [HomeController::class, 'api'])->name('api');
Route::post('/search', [HomeController::class, 'search'])->name('search');
Route::get('/embed/{type}/{id}', [HomeController::class, 'embed'])->name('embed');

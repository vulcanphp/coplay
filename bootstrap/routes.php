<?php

use Views\Explore;
use Views\Home;

/**
 * This file is responsible for defining the routes of the web application.
 *
 * @return array
 *     An array of route definitions.
 */

return [
    ['path' => '/', 'callback' => [Home::class, 'index']],
    ['path' => '/{type}/genre/{slug}', 'callback' => [Explore::class, 'genre'], 'name' => 'genre'],
    ['path' => '/{type}/language/{language}', 'callback' => [Explore::class, 'language'], 'name' => 'language'],
    ['path' => '/{type}/country/{country}', 'callback' => [Explore::class, 'country'], 'name' => 'country'],
    ['path' => '/{type}/company/{slug}', 'callback' => [Explore::class, 'company'], 'name' => 'company'],
    ['path' => '/{type}/network/{slug}', 'callback' => [Explore::class, 'network'], 'name' => 'network'],
    ['path' => '/people/{slug}', 'callback' => [Explore::class, 'people'], 'name' => 'people'],
    ['path' => '/movie/{slug?}', 'callback' => [Home::class, 'movie'], 'name' => 'movie'],
    ['path' => '/tv/{slug?}', 'callback' => [Home::class, 'tv'], 'name' => 'tv'],
    ['path' => '/watchlist', 'callback' => [Home::class, 'watchlater'], 'name' => 'watchlist'],
    ['path' => '/api', 'callback' => [Home::class, 'api'], 'name' => 'api'],
    ['path' => '/search', 'callback' => [Home::class, 'search'], 'name' => 'search'],
    ['path' => '/embed/{type}/{id}', 'callback' => [Home::class, 'embed'], 'name' => 'embed'],
    ['path' => '/*', 'callback' => 'page_not_found', 'name' => '404'],
];
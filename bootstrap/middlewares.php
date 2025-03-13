<?php

/**
 * Middleware configuration.
 *
 * This file returns an array of middleware classes used by the application.
 * Each middleware is associated with a key that can be used to reference it.
 * 
 * @return array
 *   An associative array of middleware keys and their corresponding class names.
 */
return [
    // CSRF protection middleware
    'csrf' => \Middlewares\CsrfProtectionMiddleware::class,
];

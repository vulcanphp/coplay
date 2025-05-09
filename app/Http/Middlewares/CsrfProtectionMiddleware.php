<?php

namespace App\Http\Middlewares;

use Spark\Foundation\Http\Middlewares\CsrfProtection;


/**
 * Class CsrfProtectionMiddleware
 *
 * A middleware class for CSRF protection that extends the
 * Hyper\Middlewares\CsrfProtection class.
 *
 * @package Middlewares
 */
class CsrfProtectionMiddleware extends CsrfProtection
{
    /**
     * An array of URI paths that should be excluded from CSRF verification.
     * @var array
     */
    protected array $except = [
        '/api/*' // Exclude all API routes from CSRF protection
    ];
}

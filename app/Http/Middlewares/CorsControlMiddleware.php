<?php

namespace App\Http\Middlewares;

use Spark\Foundation\Http\Middlewares\CorsAccessControl;

/**
 * A middleware class for Cross-Origin Resource Sharing (CORS) control.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
 */
class CorsControlMiddleware extends CorsAccessControl
{
    protected array $allowed = [
        /**
         * The allowed origin. An asterisk (*) is a wildcard character that will match all origins.
         *
         * @var string|array string: '*' or array:['https://example.com', ...]
         */
        'origin' => '*',

        /**
         * The allowed methods. Define explicit methods instead of using the wildcard character.
         *
         * @var array
         */
        'methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

        /**
         * The allowed headers. Define explicit headers instead of using the wildcard character.
         *
         * @var array
         */
        'headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],

        /**
         * Whether the request includes user credentials.
         *
         * @var string
         */
        'credentials' => 'true',

        /**
         * The maximum age of the CORS configuration in seconds.
         *
         * @var int
         */
        'age' => 86400,
    ];
}
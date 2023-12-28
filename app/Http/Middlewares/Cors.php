<?php

namespace App\Http\Middlewares;

use VulcanPhp\PhpRouter\Security\Cors as IMiddleware;

class Cors extends IMiddleware
{
    protected array $allowed = [
        'origin'      => '*',
        'credentials' => true,
        'age'         => 1000,
        'methods'     => ['POST', 'GET', 'OPTIONS', 'PUT', 'DELETE'],
        'headers'     => ['Accept', 'Content-Type', 'Content-Length', 'Accept-Encoding', 'X-CSRF-Token', 'Authorization']
    ];
}

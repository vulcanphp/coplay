<?php

namespace App\Http\Middlewares;

use VulcanPhp\PhpRouter\Security\BaseCsrfVerifier as IMiddleware;
use VulcanPhp\PhpRouter\Security\Token\SessionTokenProvider as TokenProvider;

class Csrf extends IMiddleware
{
    /**
     * CSRF validation will be ignored on the following urls.
     */
    protected $except = ['/api/*'];

    public function __construct()
    {
        $this->setTokenProvider(new TokenProvider);
    }
}

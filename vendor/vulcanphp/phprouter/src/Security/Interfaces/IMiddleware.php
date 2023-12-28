<?php

namespace VulcanPhp\PhpRouter\Security\Interfaces;

use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;

interface IMiddleware
{
    /**
     * @return void 
     */
    public function handle(Request $request, Response $response): void;
}

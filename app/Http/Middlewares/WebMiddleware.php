<?php

namespace App\Http\Middlewares;

use VulcanPhp\InputMaster\Request;
use VulcanPhp\InputMaster\Response;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;

class WebMiddleware implements IMiddleware
{
    public function handle(Request $request, Response $response): void
    {
    }
}

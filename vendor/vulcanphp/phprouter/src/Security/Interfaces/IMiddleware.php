<?php

namespace VulcanPhp\PhpRouter\Security\Interfaces;

use VulcanPhp\InputMaster\Request;
use VulcanPhp\InputMaster\Response;

interface IMiddleware
{
    /**
     * @return void 
     */
    public function handle(Request $request, Response $response): void;
}

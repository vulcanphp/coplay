<?php

namespace Lib\Embed\Interfaces;

use Hyper\Request;
use Hyper\Response;

/**
 * Interface IEmbedController
 * 
 * @package Lib\Embed\Interfaces
 */
interface IEmbedController
{
    /**
     * Dispatch a request to the controller.
     *
     * @param Request $request The request object.
     * 
     * @return Response The response object.
     */
    public function dispatch(Request $request): Response;
}

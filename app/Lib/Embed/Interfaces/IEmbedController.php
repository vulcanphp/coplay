<?php

namespace App\Lib\Embed\Interfaces;

use Spark\Http\Request;
use Spark\Http\Response;

/**
 * Interface IEmbedController
 * 
 * @package App\Lib\Embed\Interfaces
 */
interface IEmbedController
{
    /**
     * Dispatch a request to the controller.
     *
     * @param \Spark\Http\Request $request The request object.
     * 
     * @return \Spark\Http\Response The response object.
     */
    public function dispatch(Request $request): Response;
}

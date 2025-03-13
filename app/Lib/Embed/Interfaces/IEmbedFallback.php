<?php

namespace Lib\Embed\Interfaces;

use Hyper\Response;

/**
 * Interface IEmbedFallback
 * 
 * @package Lib\Embed\Interfaces
 */
interface IEmbedFallback
{
    /**
     * Trigger the fallback logic.
     *
     * @return Response
     */
    public function trigger(): Response;

    /**
     * Set the server.
     *
     * @param string $server
     * @return void
     */
    public function setServer(string $server): void;
}

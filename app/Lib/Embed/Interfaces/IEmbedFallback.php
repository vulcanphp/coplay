<?php

namespace App\Lib\Embed\Interfaces;

use Spark\Http\Response;

/**
 * Interface IEmbedFallback
 * 
 * @package App\Lib\Embed\Interfaces
 */
interface IEmbedFallback
{
    /**
     * Trigger the fallback logic.
     *
     * @return \Spark\Http\Response
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

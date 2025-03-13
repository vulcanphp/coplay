<?php

namespace Lib\Embed\Interfaces;

use Hyper\Response;

/**
 * Interface for embed crawlers
 * 
 * @package Lib\Embed\Interfaces
 */
interface IEmbedCrawler
{
    /**
     * Set the crawler with a specific ID and callback function
     * 
     * @param string $id The unique identifier for the crawler
     * @param callable $callback The function to be called for the crawler
     */
    public function setCrawler(string $id, $callback): void;

    /**
     * Resolve the given token and return the response
     * 
     * @param string $token The token to resolve
     * @return Response The response after resolving the token
     */
    public function resolve(string $token): Response;
}

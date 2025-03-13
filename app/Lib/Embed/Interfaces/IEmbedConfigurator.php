<?php

namespace Lib\Embed\Interfaces;

/**
 * Interface IEmbedConfigurator
 * 
 * @package Lib\Embed\Interfaces
 */
interface IEmbedConfigurator
{
    /**
     * Get a value from the configuration.
     *
     * @param string|null $key The key of the value to retrieve.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The value associated with the given key.
     */
    public function get(?string $key = null, $default = null);

    /**
     * Add a value to the configuration.
     *
     * @param string $key The key of the value to add.
     * @param mixed $value The value to add.
     * @return self
     */
    public function push(string $key, $value): self;

    /**
     * Convert a URL to a canonical format.
     *
     * @param string $url The URL to convert.
     * @return string The canonical format of the URL.
     */
    public function replaceEmbedUrlSegments(string $url): string;

    /**
     * Get the schema of the embed content.
     *
     * @return array The schema of the embed content.
     */
    public function getSchema(): array;
}


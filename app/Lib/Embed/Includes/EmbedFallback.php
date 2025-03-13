<?php

namespace Lib\Embed\Includes;

use Hyper\Response;
use Lib\Embed\Interfaces\IEmbedFallback;

/**
 * Class EmbedFallback
 *
 * This class provides a fallback implementation for the IEmbedFallback interface.
 * It is used when no other implementation is available.
 *
 * @package Lib\Embed\Includes
 */
class EmbedFallback implements IEmbedFallback
{
    /**
     * Constructor for EmbedFallback class.
     *
     * @param EmbedConfigurator|null $config Optional configuration object.
     * @param string|null $server Optional server URL.
     */
    public function __construct(protected ?EmbedConfigurator $config = null, protected ?string $server = null)
    {
    }

    /**
     * Sets the server URL.
     *
     * @param string $server The server URL to set.
     */
    public function setServer(string $server): void
    {
        $this->server = $server;
    }

    /**
     * Sets the configuration object.
     *
     * @param EmbedConfigurator $config The configuration object to set.
     */
    public function setConfigurator(EmbedConfigurator $config): void
    {
        $this->config = $config;
    }

    /**
     * Triggers the fallback logic.
     *
     * @return Response The response after executing the fallback logic.
     */
    public function trigger(): Response
    {
        if (isset($this->server)) {
            redirect($this->server);
        }

        return template('embed/fallback', ['config' => $this->config]);
    }
}


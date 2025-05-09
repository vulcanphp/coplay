<?php

namespace App\Lib\Embed\Includes;

use App\Lib\Embed\Interfaces\IEmbedFallback;
use Spark\Http\Response;

/**
 * Class EmbedFallback
 *
 * This class provides a fallback implementation for the IEmbedFallback interface.
 * It is used when no other implementation is available.
 *
 * @package App\Lib\Embed\Includes
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
     * @return \Spark\Http\Response The response after executing the fallback logic.
     */
    public function trigger(): Response
    {
        if (isset($this->server)) {
            redirect($this->server);
        }

        return view('embed/fallback', ['config' => $this->config]);
    }
}


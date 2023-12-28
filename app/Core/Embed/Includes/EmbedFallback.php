<?php

namespace App\Core\Embed\Includes;

use App\Core\Embed\Interfaces\IEmbedFallback;

class EmbedFallback implements IEmbedFallback
{
    public function __construct(protected ?EmbedConfigurator $config = null, protected ?string $server = null)
    {
    }

    public function setServer(string $server): void
    {
        $this->server = $server;
    }

    public function setConfigurator(EmbedConfigurator $config): void
    {
        $this->config = $config;
    }

    public function trigger(): string
    {
        if (isset($this->server)) {
            redirect($this->server);
        }

        return view('embed.fallback', ['config' => $this->config]);
    }
}

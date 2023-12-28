<?php

namespace App\Core\Embed\Interfaces;

interface IEmbedConfigurator
{
    public function get(?string $key = null, $default = null);

    public function push(string $key, $value): self;

    public function parseUrl(string $url): string;

    public function getSchema(): array;
}

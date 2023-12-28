<?php

namespace App\Core\Embed\Interfaces;

interface IEmbedFallback
{
    public function trigger(): string;

    public function setServer(string $server): void;
}

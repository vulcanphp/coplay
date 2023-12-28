<?php

namespace App\Core\Embed\Interfaces;

interface IEmbedCrawler
{
    public function setCrawler(string $id, $callback): void;

    public function resolve(string $token): string;
}

<?php

namespace VulcanPhp\EasyCurl\Interfaces;

interface ICurlResponse
{
    public function getResponse(?string $key = null): mixed;

    public function getStatus(): int;

    public function getBody(): string;

    public function getJson(): array;

    public function getLength(): int;

    public function getLastUrl(): string;
}

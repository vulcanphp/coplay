<?php

namespace VulcanPhp\FastCache\Interfaces;

interface IEngine
{
    public function newCache(string $name): ICacheHandler;

    public function flush(): void;
}

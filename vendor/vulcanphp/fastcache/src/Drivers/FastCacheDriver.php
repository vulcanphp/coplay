<?php

namespace VulcanPhp\FastCache\Drivers;

use VulcanPhp\FastCache\Engine\SimpleCache\SimpleCache;
use VulcanPhp\FastCache\Interfaces\ICacheDriver;
use VulcanPhp\FastCache\Interfaces\ICacheHandler;
use VulcanPhp\FastCache\Interfaces\IEngine;

class FastCacheDriver implements ICacheDriver
{
    protected IEngine $engine;

    public function __construct(array $setup = [])
    {
        $this->engine = new SimpleCache($setup);
    }

    public function createCache(string $name): ICacheHandler
    {
        return $this->getEngine()
            ->newCache($name);
    }

    public function getEngine(): IEngine
    {
        return $this->engine;
    }
}

<?php

namespace VulcanPhp\FastCache;

use VulcanPhp\FastCache\Drivers\FastCacheDriver;
use VulcanPhp\FastCache\Interfaces\ICache;
use VulcanPhp\FastCache\Interfaces\ICacheDriver;
use VulcanPhp\FastCache\Interfaces\ICacheHandler;

class Cache implements ICache
{
    public static Cache $instance;

    protected ICacheDriver $Driver;

    public function __construct(?ICacheDriver $Driver = null)
    {
        $this->setDriver($Driver ?: new FastCacheDriver);
    }

    public static function init(...$args): Cache
    {
        return self::$instance = new Cache(...$args);
    }

    public function setDriver(ICacheDriver $Driver): self
    {
        $this->Driver = $Driver;
        return $this;
    }

    public function create(...$args): ICacheHandler
    {
        return $this->getDriver()
            ->createCache(...$args);
    }

    public function getDriver(): ICacheDriver
    {
        return $this->Driver;
    }
}

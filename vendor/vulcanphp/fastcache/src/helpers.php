<?php

use VulcanPhp\FastCache\Cache;
use VulcanPhp\FastCache\Drivers\FastCacheDriver;
use VulcanPhp\FastCache\Engine\SimpleCache\SimpleCacheHandler;
use VulcanPhp\FastCache\Interfaces\ICache;
use VulcanPhp\FastCache\Interfaces\ICacheDriver;
use VulcanPhp\FastCache\Interfaces\ICacheHandler;
use VulcanPhp\FastCache\Interfaces\IEngine;
use VulcanPhp\FastCache\Interfaces\ISiteCache;
use VulcanPhp\FastCache\SiteCache;

if (!function_exists('cache_init')) {
    function cache_init($cacheConfig = []): ICache
    {
        return Cache::init(new FastCacheDriver($cacheConfig));
    }
}

if (!function_exists('cache_create')) {
    function cache_create(?ICacheDriver $Driver = null): ICache
    {
        return new Cache($Driver);
    }
}

if (!function_exists('cache')) {
    function cache(string $name = 'default'): ICacheHandler
    {
        return Cache::$instance->create($name);
    }
}

if (!function_exists('cache_driver')) {
    function cache_driver(): ICacheDriver
    {
        return Cache::$instance->getDriver();
    }
}

if (!function_exists('cache_engine')) {
    function cache_engine(): IEngine
    {
        return Cache::$instance->getDriver()->getEngine();
    }
}

if (!function_exists('cache_handler')) {
    function cache_handler(string $filepath): ICacheHandler
    {
        return new SimpleCacheHandler($filepath);
    }
}

if (!function_exists('init_site_cache')) {
    function init_site_cache(...$args): ISiteCache
    {
        return SiteCache::setup(...$args);
    }
}

if (!function_exists('serve_site_cache')) {
    function serve_site_cache(): void
    {
        SiteCache::$instance->serve();
    }
}

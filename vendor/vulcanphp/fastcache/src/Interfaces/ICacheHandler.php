<?php

namespace VulcanPhp\FastCache\Interfaces;

interface ICacheHandler
{
    public function hasCache(string $key, bool $eraseExpired = false): bool;

    public function retrieve($keys, bool $eraseExpired = false);

    public function retrieveAll(bool $eraseExpired = false): array;

    public function load(string $key, callable $callback,  ?string $expire = null);

    public function store(string $key, $data,  ?string $expire = null): ICacheHandler;

    public function erase($keys): ICacheHandler;

    public function eraseExpired(): ICacheHandler;

    public function flush(): ICacheHandler;

    public function close(): ICacheHandler;
}

<?php

namespace VulcanPhp\FastCache\Interfaces;

interface ICache
{
    public function create(...$args): ICacheHandler;

    public function getDriver(): ICacheDriver;
}

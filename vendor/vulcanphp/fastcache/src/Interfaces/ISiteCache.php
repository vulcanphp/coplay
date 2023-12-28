<?php

namespace VulcanPhp\FastCache\Interfaces;

interface ISiteCache
{
    public function serve(): void;

    public function clean(): void;

    public function flush(): void;
}

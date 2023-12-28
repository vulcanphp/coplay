<?php

namespace VulcanPhp\Core\Foundation\Interfaces;

interface IKernel
{
    public function boot(): void;

    public function shutdown(): void;
}

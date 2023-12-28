<?php

namespace App\Core\Embed\Interfaces;

interface IAutoUpdate
{
    public function isExpired(): bool;

    public function check(bool $force = false): void;
}

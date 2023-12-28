<?php

namespace VulcanPhp\Core\Crypto\Password\Drivers;

interface IPasswordDriver
{
    public function password(string $plain): string;
    public function passwordVerify(string $plain, string $hash): bool;
}

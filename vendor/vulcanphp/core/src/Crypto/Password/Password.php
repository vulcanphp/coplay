<?php

namespace VulcanPhp\Core\Crypto\Password;

use VulcanPhp\Core\Crypto\Password\Drivers\IPasswordDriver;
use VulcanPhp\Core\Crypto\Password\Drivers\PasswordDefault;

class Password
{
    protected IPasswordDriver $Driver;

    public function __construct(?IPasswordDriver $Driver = null)
    {
        $this->setDriver($Driver ?: new PasswordDefault);
    }

    public static function create(...$args): Password
    {
        return new Password(...$args);
    }

    public static function hash($Driver = null, ...$args): string
    {
        return self::create($Driver)->generate(...$args);
    }

    public static function validate($Driver = null, ...$args): bool
    {
        return self::create($Driver)->verify(...$args);
    }

    public function generate(string $plain): string
    {
        return $this->getDriver()->password($plain);
    }

    public function verify(string $plain, string $hash): bool
    {
        return $this->getDriver()->passwordVerify($plain, $hash);
    }

    public function setDriver(IPasswordDriver $Driver): self
    {
        $this->Driver = $Driver;
        return $this;
    }

    public function getDriver(): IPasswordDriver
    {
        return $this->Driver;
    }
}

<?php

namespace VulcanPhp\EasyCurl;

use VulcanPhp\EasyCurl\Interfaces\ICurl;
use VulcanPhp\EasyCurl\Drivers\EasyCurlDriver;
use VulcanPhp\EasyCurl\Interfaces\ICurlDriver;

class EasyCurl implements ICurl
{
    protected ICurlDriver $Driver;

    public function __construct(?ICurlDriver $Driver = null)
    {
        $this->setDriver($Driver ?: new EasyCurlDriver());
    }

    public static function create(...$args): EasyCurl
    {
        return new EasyCurl(...$args);
    }

    public function setDriver(ICurlDriver $Driver): void
    {
        $this->Driver = $Driver;
    }

    public function getDriver(): ICurlDriver
    {
        return $this->Driver;
    }

    public function __call($name, $arguments)
    {
        return call_user_func([$this->getDriver(), $name], ...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return call_user_func([EasyCurl::create()->getDriver(), $name], ...$arguments);
    }
}

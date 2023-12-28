<?php

namespace VulcanPhp\FileSystem\Includes;

class BaseHandler
{
    public static $instance;
    protected $Handler;

    public static function create(...$args): self
    {
        return new static(...$args);
    }

    public static function choose(...$args): self
    {
        return static::create(...$args);
    }

    public static function select(...$args): self
    {
        return static::create(...$args);
    }

    public static function init(...$args): self
    {
        return static::$instance = static::create(...$args);
    }

    public function getHandler()
    {
        return $this->Handler;
    }

    public function __call($name, $arguments)
    {
        return call_user_func([$this->getHandler(), $name], ...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $filepath = array_shift($arguments);

        return call_user_func([static::create($filepath)->getHandler(), $name], ...$arguments);
    }
}

<?php

namespace VulcanPhp\PhpRouter\Callback;

use VulcanPhp\PhpRouter\Callback\Exceptions\UndefinedMethod;

trait UniversalCall
{
    protected array $universal_data = [];

    public function __call($name, $arguments)
    {
        return $this->magicCall($name, $arguments, $this);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::magicCall($name, $arguments, static::class);
    }

    protected static function magicCall($name, $arguments, $class)
    {
        // all relevant method collection
        $matched = [];

        // find relevant methods
        foreach (['get', 'set'] as $flag) {
            // match method with flag
            $method = $flag . ucfirst($name);
            if (CallbackHandler::exists([$class, $method])) {
                $parameter = CallbackHandler::parameters([$class, $method]);
                $matched[] = [
                    'name' => $method,
                    'parameter' => $parameter,
                    'required' => array_filter($parameter, fn ($param) => !$param->isOptional() || !$param->isDefaultValueAvailable())
                ];
            }
        }

        // match method with same parameter count
        foreach ($matched as $method) {
            if (count($method['parameter']) == count($arguments)) {
                return CallbackHandler::load([$class, $method['name']], ...$arguments);
            }
        }

        // match method with optional parameter count
        foreach ($matched as $method) {
            if (count($method['required']) == count($arguments)) {
                return CallbackHandler::load([$class, $method['name']], ...$arguments);
            }
        }

        // match method with default value available parameter count
        foreach ($matched as $method) {
            if (count($arguments) > count($method['required'])) {
                return CallbackHandler::load([$class, $method['name']], ...$arguments);
            }
        }

        throw new UndefinedMethod(CallbackHandler::name($class) . '->' . $name . '() does not exists');
    }

    public function __set($name, $value = null)
    {
        $this->universal_data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->universal_data[$name] ?? null;
    }

    public function __isset($name): bool
    {
        return array_key_exists($name, $this->universal_data) === true;
    }

    public function __unset($name)
    {
        unset($this->universal_data[$name]);
    }
}

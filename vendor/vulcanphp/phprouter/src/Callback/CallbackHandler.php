<?php

namespace VulcanPhp\PhpRouter\Callback;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionNamedType;
use VulcanPhp\PhpRouter\Callback\Exceptions\UndefinedClass;
use VulcanPhp\PhpRouter\Callback\Exceptions\UndefinedMethod;

class CallbackHandler
{
    public static function reflect(array $params, $callback, $instances = []): array
    {
        $ReflectionParameters = self::parameters($callback);
        if (empty($ReflectionParameters)) {
            return array_values($params);
        }

        $inputs = [];
        foreach ($ReflectionParameters as $key => $param) {
            if (isset($inputs[$param->getName()])) {
                continue;
            }

            $value = $params[$param->getName()] ?? ($params[$key] ?? (array_values($params)[$key] ?? null));

            if ($param->getType() instanceof ReflectionNamedType && !$param->getType()->isBuiltin()) {
                $class = new ReflectionClass($param->getType()->getName());
                foreach ($instances as $instance) {
                    $parameter = is_array($instance) ? $instance['parameter'] : $instance;
                    $callback = is_array($instance) ? $instance['callback'] : null;
                    if (
                        (is_object($parameter) && $class->isInstance($parameter))
                        || (is_string($parameter) && $class->isSubclassOf($parameter))
                    ) {
                        $inputs[$param->getName()] = $callback !== null ? self::load($callback, $param, $params, $key, $value) : $parameter;
                        break;
                    }
                }
            } else {
                $inputs[$param->getName()] = $value;
            }
        }

        return array_values($inputs);
    }

    public static function parameters($callback): array
    {
        if (is_array($callback) && self::exists($callback)) {
            if (!is_string($callback[0])) {
                $callback[0] = get_class($callback[0]);
            }
            return (new ReflectionMethod(...$callback))->getParameters();
        } elseif ((is_string($callback) || is_object($callback)) && self::exists($callback)) {
            return (new ReflectionFunction($callback))->getParameters();
        }

        return [];
    }

    public static function exists($callback): bool
    {
        return (
            (is_string($callback) && function_exists($callback))
            || (is_object($callback) && ($callback instanceof Closure))
        )
            || (is_array($callback) && method_exists($callback[0], $callback[1]));
    }

    public static function load($callback, ...$params)
    {
        if (!self::exists($callback)) {
            throw new UndefinedMethod((is_array($callback) ? (!is_string($callback[0]) ? get_class($callback[0]) : (string) $callback[0]) . '->' . $callback[1] : $callback) . '() does not exists');
        }

        return call_user_func($callback, ...array_values($params));
    }

    public static function create(string $class, ...$args)
    {
        if (!class_exists($class)) {
            throw new UndefinedClass('Class: ' . $class . ' does not exists');
        }

        return new $class(...array_values($args));
    }

    public static function name($callback): string
    {
        if (is_array($callback) && is_string($callback[0]) && class_exists($callback[0])) {
            return $callback[0];
        } elseif (is_array($callback) && !is_string($callback[0])) {
            return get_class($callback[0]);
        } elseif (is_object($callback)) {
            return get_class($callback);
        }

        return (string) $callback ?? '';
    }
}

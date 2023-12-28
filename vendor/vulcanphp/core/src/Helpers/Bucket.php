<?php

namespace VulcanPhp\Core\Helpers;

class Bucket
{
    public static Bucket $store;

    public array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public static function init(...$args): Bucket
    {
        return self::$store = new Bucket(...$args);
    }

    public static function has(string $type, $key = null): bool
    {
        return $key !== null
            ? isset(self::$store->data[$type][$key]) && !empty(self::$store->data[$type][$key])
            : isset(self::$store->data[$type]) && !empty(self::$store->data[$type]);
    }

    public static function set(string $type, $value, $key = null): self
    {
        $key !== null
            ? self::$store->data[$type][$key] = $value
            : self::$store->data[$type] = $value;

        return self::$store;
    }

    public static function push(string $type, $value, $key = null): self
    {
        if (!isset(self::$store->data[$type])) {
            self::$store->data[$type] = [];
        }

        if ($key !== null && !isset(self::$store->data[$type][$key])) {
            self::$store->data[$type][$key] = [];
        }

        $key !== null
            ? array_push(self::$store->data[$type][$key], $value)
            : array_push(self::$store->data[$type], $value);

        return self::$store;
    }

    public static function unshift(string $type, $value, $key = null): self
    {
        if (!isset(self::$store->data[$type])) {
            self::$store->data[$type] = [];
        }

        $key !== null
            ? array_unshift(self::$store->data[$type][$key], $value)
            : array_unshift(self::$store->data[$type], $value);

        return self::$store;
    }

    public static function pop(string $type, $key = null)
    {
        return $key !== null
            ? array_pop(self::$store->data[$type][$key])
            : array_pop(self::$store->data[$type]);
    }

    public static function shift(string $type, $key = null)
    {
        return $key !== null
            ? array_shift(self::$store->data[$type][$key])
            : array_shift(self::$store->data[$type]);
    }

    public static function get(string $type, $key = null, $default = null)
    {
        return $key !== null
            ? (self::$store->data[$type][$key] ?? $default)
            : (self::$store->data[$type] ?? $default);
    }

    public static function is(...$args): bool
    {
        return boolval(self::get(...$args)) === true;
    }

    public static function last(string $type, $key = null)
    {
        return $key !== null
            ? Arr::last(self::$store->data[$type][$key])
            : Arr::last(self::$store->data[$type]);
    }

    public static function first(string $type, $key = null)
    {
        return $key !== null
            ? Arr::first(self::$store->data[$type][$key])
            : Arr::first(self::$store->data[$type]);
    }

    public static function load(string $type, $callback, $key = null)
    {
        return $key !== null
            ? (self::$store->data[$type][$key] ??= call_user_func($callback))
            : (self::$store->data[$type] ??= call_user_func($callback));
    }

    public static function remove(string $type, $key = null): self
    {
        if ($key !== null) {
            unset(self::$store->data[$type][$key]);
        } else {
            unset(self::$store->data[$type]);
        }

        return self::$store;
    }
}

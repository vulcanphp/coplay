<?php

namespace VulcanPhp\Core\Helpers;

/**
 * @see https://github.com/bayfrontmedia/php-array-helpers
 */
class Arr
{
    public static function isAssoc($array)
    {
        if (!is_array($array) || $array === []) {
            return false;
        }

        return array_keys($array) !== range(0, count($array) - 1);
    }

    public static function isMulti(array $array): bool
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }

    public static function toObject($array)
    {
        $result = json_decode(json_encode($array), false);
        return is_object($result) ? $result : null;
    }

    public static function toArray($object)
    {
        $result = json_decode(json_encode($object), true);
        return is_array($result) ? $result : null;
    }

    public static function dump($var)
    {
        if (is_string($var)) {
            return str_split($var);
        }

        if (is_object($var)) {
            return json_decode(json_encode($var), true);
        }

        return null;
    }

    public static function first($array)
    {
        return !empty($array) ? ($array[array_keys($array)[0]] ?? null) : null;
    }

    public static function last($array)
    {
        return !empty($array) ? ($array[array_keys($array)[sizeof($array) - 1]] ?? null) : null;
    }

    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {

            if (is_array($value)) {

                $results = array_merge($results, self::dot($value, $prepend . $key . '.'));
            } else {

                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    public static function undot(array $array): array
    {
        $return = [];

        foreach ($array as $key => $value) {

            self::set($return, $key, $value);
        }

        return $return;
    }

    public static function set(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);

        while (count($keys) > 1) {

            $key = array_shift($keys);

            if (!isset($array[$key]) || !is_array($array[$key])) {

                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
    }

    public static function has(array $array, string $key): bool
    {
        return null !== self::get($array, $key);
    }

    public static function get(array $array, string $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return $default;
            }
            $array = $array[$segment];
        }

        return $array;
    }

    public static function pluck(array $array, string $value, string $key = null): array
    {
        $results = [];
        foreach ($array as $item) {
            $itemValue = self::get($item, $value);
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey           = self::get($item, $key);
                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }

    public static function forget(array &$array, $keys): void
    {
        $original = &$array;
        foreach ((array) $keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                }
            }

            unset($array[array_shift($parts)]);

            // Clean up after each iteration
            $array = &$original;
        }
    }

    public static function except(array $array, $keys): array
    {
        return array_diff_key($array, array_flip((array) $keys));
    }

    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    public static function missing(array $array, $keys): array
    {
        return array_values(array_flip(array_diff_key(array_flip((array) $keys), $array)));
    }

    public static function isMissing(array $array, $keys): bool
    {
        return (self::missing($array, $keys)) ? true : false;
    }

    public static function multisort(array $array, string $key, bool $descending = false, $sort = SORT_NUMERIC): array
    {
        $columns = array_column($array, $key);

        if (false === $descending) {
            array_multisort($columns, SORT_ASC, $array, $sort);
        } else {
            array_multisort($columns, SORT_DESC, $array, $sort);
        }

        return $array;
    }

    public static function renameKeys(array $array, array $keys): array
    {
        $new_array = [];

        foreach ($array as $k => $v) {
            if (array_key_exists($k, $keys)) {
                $new_array[$keys[$k]] = $v;
            } else {
                $new_array[$k] = $v;
            }
        }

        return $new_array;
    }

    public static function order(array $array, array $order): array
    {
        return self::only(array_replace(array_flip($order), $array), array_keys($array));
    }

    public static function query(array $array): string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    public static function getAnyValues(array $array, array $values): array
    {
        return array_intersect($values, Arr::dot($array));
    }

    public static function unique(array $array, $column = null)
    {
        $values = array();
        if ($column !== null) {
            foreach ($array as &$v) {
                if (is_array($column)) {
                    $x_column = '';

                    foreach ($column as $cl) {
                        $x_column .= '_' . $v[$cl];
                    }

                    if (!isset($values[$x_column])) {
                        $values[$x_column] = &$v;
                    }
                } else {
                    if (!isset($values[$v[$column]])) {
                        $values[$v[$column]] = &$v;
                    }
                }
            }
            $values = array_values($values);
        } else {
            $values = array_unique($array, SORT_REGULAR);
        }

        return $values;
    }

    public static function avarage(array $array)
    {
        $array = array_filter($array);
        return array_sum($array) / count($array);
    }

    public static function hasAnyValues(array $array, array $values): bool
    {
        return !empty(self::getAnyValues($array, $values));
    }

    public static function hasAllValues(array $array, array $values): bool
    {
        return count(array_intersect($values, Arr::dot($array))) == count($values);
    }
}

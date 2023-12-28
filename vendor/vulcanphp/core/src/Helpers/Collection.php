<?php

namespace VulcanPhp\Core\Helpers;

class Collection
{
    protected array $values = array();

    public function __construct(...$values)
    {
        if (count($values) === 1) {
            $values = $values[0];
        }
        if (is_array($values) && !empty($values)) {
            $this->values = $values;
        }
    }

    public function clone(): self
    {
        return clone $this;
    }

    public function count(): int
    {
        if (!isset($this->values) || !is_array($this->values) || empty($this->values)) {
            return 0;
        }
        return count($this->values);
    }

    public function keys()
    {
        return array_keys($this->values);
    }

    public function first(?string $key = null, $default = null)
    {
        $first = Arr::first($this->values);
        return $key !== null && $first !== null ? Arr::get($first, $key, $default) : $first;
    }

    public function last(?string $key = null, $default = null)
    {
        $last = Arr::last($this->values);
        return $key !== null && $last !== null ? Arr::get($last, $key, $default) : $last;
    }

    public function in($value): bool
    {
        return in_array($value, $this->values);
    }

    public function column(string $column): ?array
    {
        return array_column($this->values, $column);
    }

    public function exists($key): bool
    {
        return Arr::has($this->values, $key);
    }

    public function get($key, $default = null)
    {
        return Arr::get($this->values, $key, $default);
    }

    public function order(array $order)
    {
        if (Arr::isMulti($this->values)) {
            $this->map(function ($item) use ($order) {
                return Arr::order($item, $order);
            });
        } else {
            $this->values = Arr::order($this->values, $order);
        }
        return $this;
    }

    public function pushAll(array $values): self
    {
        foreach ($values as $value) {
            array_push($this->values, $value);
        }
        return $this;
    }

    public function push($value, $key = null): self
    {
        if ($key !== null) {
            if (!isset($this->values[$key])) {
                $this->values[$key] = [];
            }
            $this->values[$key][] = $value;
        } else {
            array_push($this->values, $value);
        }
        return $this;
    }

    public function all(): array
    {
        return $this->values;
    }

    public function combine(array $keys = array()): self
    {
        $values = array_combine($keys, $this->values);
        return new Collection($values);
    }

    public function unique($column = null): self
    {
        $values = Arr::unique($this->values, $column);
        return new Collection($values);
    }

    public function each(\Closure $callback): self
    {
        array_map($callback, $this->values, $this->keys());
        return $this;
    }

    public function filter(?\Closure $callback = null): self
    {
        if ($callback !== null) {
            $this->values = array_filter($this->values, $callback, ARRAY_FILTER_USE_BOTH);
        } else {
            $this->values = array_filter($this->values);
        }
        return $this;
    }

    public function map(\Closure $callback): self
    {
        $this->values = array_map($callback, $this->all(), $this->keys());
        return $this;
    }

    public function mapWithKeys(\Closure $callback): self
    {
        $values = [];

        foreach (array_map($callback, $this->all(), $this->keys()) as $array) {
            foreach ($array as $k => $v) {
                $values[$k] = $v;
            }
        }

        $this->values = $values;

        return $this;
    }

    public function merge(...$array): self
    {
        $this->values = array_merge($this->values, ...$array);
        return $this;
    }

    public function select(\Closure $callback): Collection
    {
        return new Collection(array_map($callback, $this->values, $this->keys()));
    }

    public function only(array $keys, bool $multi = true): self
    {
        if (Arr::isMulti($this->values) && $multi) {
            $this->map(function ($item) use ($keys) {
                return Arr::only($item, $keys);
            });
        } else {
            $this->values = Arr::only($this->values, $keys);
        }

        return $this;
    }

    public function except(array $keys): self
    {
        if (Arr::isMulti($this->values)) {
            $this->map(function ($item) use ($keys) {
                return Arr::except($item, $keys);
            });
        } else {
            $this->values = Arr::except($this->values, $keys);
        }
        return $this;
    }

    public function set($key, $value): self
    {
        Arr::set($this->values, $key, $value);
        return $this;
    }

    public function add($object, $key = null): self
    {
        if ($key === null) {
            $this->values[] = $object;
        } else {
            if (isset($this->values[$key])) {
                throw new \Exception("Key $key already in use.");
            } else {
                $this->values[$key] = $object;
            }
        }
        return $this;
    }

    public function unshift($value): self
    {
        array_unshift($this->values, $value);
        return $this;
    }

    public function pop()
    {
        return array_pop($this->values);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function contains($find): bool
    {
        return $this->indexOf($find) !== null;
    }

    public function indexOf($find)
    {
        $regx = is_array($find) ? strpos($find[key($find)], '%') !== false : strpos($find, '%') !== false;

        foreach ($this->values as $key => $element) {
            if (is_array($element) && is_string($find) && in_array($find, $element)) {
                return $key;
            } elseif (is_array($element) && is_array($find) && (isset($element[key($find)]) && $element[key($find)] === $find[key($find)])) {
                return $key;
            } elseif (is_array($element) && is_array($find) && (isset($element[key($find)]) && is_array($element[key($find)]) && in_array($find[key($find)], $element[key($find)]))) {
                return $key;
            } elseif ($regx && is_array($element) && is_array($find) && (isset($element[key($find)]) && like_match($find[key($find)], $element[key($find)]))) {
                return $key;
            } elseif ($regx && is_string($element) && is_string($find) && like_match($find, $element)) {
                return $key;
            } elseif (is_string($element) && is_string($find) && $element === $find) {
                return $key;
            }
        }

        return null;
    }

    public function update($value, $find): self
    {
        $index = $this->indexOf($find);
        if ($index !== null) {
            return $this->set($index, $value);
        }

        return $this;
    }

    public function find($find)
    {
        $index = $this->indexOf($find);
        if ($index !== null) {
            return $this->get($index);
        }
        return null;
    }

    public function when($find): self
    {
        $index = $this->indexOf($find);
        if ($index !== null) {
            $this->values = $this->get($index);
        }
        return $this;
    }

    public function findAndPush($find, array $push): self
    {
        $index = $this->indexOf($find);
        if ($index !== null) {
            foreach ($push as $key => $value) {
                if (!isset($this->values[$index][$key])) {
                    $this->values[$index][$key] = [];
                }
                $this->values[$index][$key][] = $value;
            }
        }

        return $this;
    }

    public function trim(int $size): self
    {
        $trim = array_chunk($this->values, $size, true);
        if (!empty($trim) && isset($trim[0])) {
            $this->values = $trim[0];
        }
        return $this;
    }

    public function splice(int $offset, int $length): array
    {
        return array_splice($this->values, $offset, $length);
    }

    public function slice(int $offset, int $length, bool $collect = false)
    {
        $values = array_slice($this->values, $offset, $length);
        return $collect === true ? new Collection($values) : $values;
    }

    public function asort(): self
    {
        asort($this->values);
        return $this;
    }

    public function usort(callable $callback): self
    {
        usort($this->values, $callback);
        return $this;
    }

    public function ksort(): self
    {
        ksort($this->values);
        return $this;
    }

    public function sort(): self
    {
        sort($this->values);
        return $this;
    }

    public function random(int $count = 1)
    {
        $data  = array();
        $total = $this->count() - 1;

        for ($i = 1; $i <= $count; $i++) {
            $data[] = $this->values[rand(0, $total)] ?? [];
        }

        $data = array_filter($data);

        if ($count > 1 && !empty($data)) {
            return $data;
        }

        return $data[0] ?? [];
    }

    public function multisort(string $key, bool $desc = false): self
    {
        $this->values = Arr::multisort($this->values, $key, $desc);
        return $this;
    }

    public function renameKeys(array $keys): self
    {
        if (Arr::isMulti($this->values)) {
            $this->map(function ($item) use ($keys) {
                return Arr::renameKeys($item, $keys);
            });
        } else {
            $this->values = Arr::renameKeys($this->values, $keys);
        }

        return $this;
    }

    public function remove($key): self
    {
        Arr::forget($this->values, $key);
        return $this;
    }

    public function findAndRemove($find): self
    {
        $key = $this->indexOf($find);

        if ($key !== null) {
            Arr::forget($this->values, $key);
        }

        return $this;
    }

    public function to(string $to = 'array')
    {
        switch ($to) {
            case 'json':
                return json_encode($this->all(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            case 'object':
                return Arr::toObject($this->all());
        }

        return $this->all();
    }

    public function join(string $separator)
    {
        return join($separator, $this->all());
    }

    public function values()
    {
        return array_values($this->values);
    }

    public function chunk(int $count): Collection
    {
        return new Collection(array_chunk($this->values, $count));
    }

    public function responseInJson()
    {
        return response()->json($this->all());
    }
}

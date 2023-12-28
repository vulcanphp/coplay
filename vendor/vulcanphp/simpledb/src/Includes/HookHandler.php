<?php

namespace VulcanPhp\SimpleDb\Includes;

class HookHandler
{
    protected array $hooks = [];

    protected function add(string $key, string $name, $callback): self
    {
        if (!isset($this->hooks[$key][$name])) {
            $this->hooks[$key][$name] = [];
        }

        array_push($this->hooks[$key][$name], $callback);

        return $this;
    }

    protected function get(string $key, string $name): array
    {
        return $this->hooks[$key][$name] ?? [];
    }

    public function hasAction(string $name): bool
    {
        return !empty($this->get('action', $name));
    }

    public function hasFilter(string $name): bool
    {
        return !empty($this->get('filter', $name));
    }

    public function hasFallback(string $name, string $key): bool
    {
        return !empty($this->get('fallback_' . $name, $key));
    }

    public function action(string $name, callable $callback): self
    {
        return $this->add('action', $name, $callback);
    }

    public function filter(string $name, callable $callback): self
    {
        return $this->add('filter', $name, $callback);
    }

    public function fallback(string $name, string $key, callable $callback): self
    {
        return $this->add('fallback_' . $name, $key, $callback);
    }

    public function doAction(string $name, ...$args): void
    {
        if ($this->hasAction($name)) {
            foreach ($this->get('action', $name) as $event) {
                call_user_func($event, ...$args);
            }
        }
    }

    public function applyFilters(string $name, $data)
    {
        if ($this->hasFilter($name)) {
            foreach ($this->get('filter', $name) as $event) {
                $data = call_user_func($event, $data);
            }
        }

        return $data;
    }

    public function getFallback(string $name, string $key, ...$data)
    {
        if ($this->hasFallback($name, $key)) {
            return call_user_func($this->get('fallback_' . $name, $key)[0], ...$data);
        }
    }
}

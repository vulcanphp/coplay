<?php

namespace App\Core;

class Configurator
{
    public static Configurator $instance;
    protected array $data = [];
    protected bool $isChanged = false;

    public function __construct(protected string $filepath)
    {
        self::$instance = $this;

        if (file_exists($this->filepath)) {
            $this->data = (array) json_decode(
                file_get_contents($this->filepath),
                true
            );
        }
    }

    public static function configure(...$args): Configurator
    {
        return new Configurator(...$args);
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function is(string $key): bool
    {
        return isset($this->data[$key]) && boolval($this->data[$key]) === true;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): self
    {
        if (!$this->has($key) || $this->get($key) != $value) {
            $this->isChanged    = true;
            $this->data[$key]   = $value;
        }

        return $this;
    }

    public function remove(string $key): self
    {
        $this->isChanged = true;

        unset($this->data[$key]);

        return $this;
    }

    public function setup(array $config): self
    {
        foreach ($config as $key => $value) {
            $this->set($key, $value);
        }

        return $this;
    }

    public function __destruct()
    {
        if ($this->isChanged) {
            file_put_contents(
                $this->filepath,
                json_encode($this->data, JSON_UNESCAPED_UNICODE)
            );
        }
    }
}

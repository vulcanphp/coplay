<?php

namespace App\Core\Tmdb\Model;

class TmdbContent
{
    public function __construct(protected array $data)
    {
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function has(string $key)
    {
        return isset($this->data[$key]);
    }

    public function toArray()
    {
        return $this->data;
    }

    // "backdrop"  => ["w300", "w780", "w1280", "original"],
    // "logo"      => ["w45", "w92", "w154", "w185", "w300", "w500", "original"],
    // "poster"    => ["w92", "w154", "w185", "w342", "w500", "w780", "original"],
    // "profile"   => ["w45", "w185", "h632", "original"],
    // "still"     => ["w92", "w185", "w300", "original"]
    public function getImageUrl(string $size = 'w185'): string
    {
        return 'https://image.tmdb.org/t/p/'
            . (['poster' => 'w185', 'backdrop' => 'w780', 'profile' => 'w45', 'still' => 'w185'][$size] ?? $size);
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __call($name, $arguments)
    {
        $callback = array_shift($arguments);

        if (isset($callback)) {
            array_unshift($arguments, $this->get($name));

            return call_user_func($callback, ...$arguments);
        }

        return $this->get($name);
    }
}

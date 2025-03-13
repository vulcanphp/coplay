<?php

namespace Lib\Tmdb\Model;

/**
 * A class to represent data from the The Movie Database (TMDB) API.
 *
 * This class provides a simple interface to access the data
 * returned by the TMDB API. It provides methods to access
 * the data as an array, as individual properties, and as
 * an iterable.
 *
 * @package Lib\Tmdb\Model
 */
class TmdbContent
{
    /**
     * Create a new instance.
     *
     * @param  array  $data
     */
    public function __construct(protected array $data)
    {
    }

    /**
     * Get the specified key from the data array.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Set the specified key in the data array.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Check if the specified key exists in the data array.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Get the data array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Constructs a URL for an image hosted on TMDB with the specified size.
     * 
     * EXAMPLE IMAGE SIZES:
     * "backdrop"  => ["w300", "w780", "w1280", "original"],
     * "logo"      => ["w45", "w92", "w154", "w185", "w300", "w500", "original"],
     * "poster"    => ["w92", "w154", "w185", "w342", "w500", "w780", "original"],
     * "profile"   => ["w45", "w185", "h632", "original"],
     * "still"     => ["w92", "w185", "w300", "original"]
     *
     * @param string $size The desired size of the image. Default is 'w185'.
     * @param string|null $imagePath The path to the image.
     * @return string The complete URL of the image with the specified size.
     */
    public function getImageUrl(string $size = 'w185', ?string $imagePath = null): string
    {
        $imagePath ??= ''; // Set $imagePath to an empty string if it is null
        return 'https://image.tmdb.org/t/p/'
            . (['poster' => 'w185', 'backdrop' => 'w780', 'profile' => 'w45', 'still' => 'w185'][$size] ?? $size)
            . $imagePath;
    }

    /**
     * Get the specified key from the data array.
     * 
     * @param string $name The key to retrieve.
     * @return mixed The value associated with the given key, or null if the key does not exist.
     */
    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Determine if a given key exists in the data array.
     *
     * This magic method allows for the use of `isset()` on the object
     * to check if a specified key exists in the underlying data array.
     *
     * @param string $name The key to check for existence.
     * @return bool True if the key exists, false otherwise.
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Magic method to set a value in the data array by key.
     *
     * This method allows setting the value of a specified key
     * in the underlying data array using object property syntax.
     *
     * @param string $name The key of the value to set.
     * @param mixed $value The value to set for the specified key.
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Magic method to handle dynamic method calls on the object.
     *
     * This method provides a mechanism to call a function dynamically 
     * with the value associated with the specified key in the data array.
     *
     * @param string $name The name of the method being called.
     * @param array $arguments The arguments passed to the method call.
     *  The first argument is expected to be a callable.
     * @return mixed The result of the callable if provided, or the value of the key.
     */
    public function __call($name, $arguments)
    {
        // Extract the callback function from the arguments
        $callback = array_shift($arguments);

        // Check if the callback is provided
        if (isset($callback)) {
            // Prepend the value associated with the key to the arguments
            array_unshift($arguments, $this->get($name));

            // Call the provided callback with the arguments
            return call_user_func($callback, ...$arguments);
        }

        // Return the value associated with the key if no callback is provided
        return $this->get($name);
    }
}

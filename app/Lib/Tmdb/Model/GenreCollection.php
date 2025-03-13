<?php

namespace Lib\Tmdb\Model;

use Hyper\Utils\Collect;

/**
 * Class GenreCollection
 *
 * A collection of genres that are associated with a movie or TV show.
 *
 * @package Lib\Tmdb\Model
 */
class GenreCollection
{
    /**
     * @var Collect The collection of genres.
     */
    private Collect $genres;

    /**
     * GenreCollection constructor.
     *
     * Initializes the collection of genres by mapping genre IDs to their names.
     *
     * @param array $genres The list of genres to be stored.
     */
    public function __construct(array $genres)
    {
        // Convert the genres array to a collection and map each genre ID to its name.
        $this->genres = collect($genres)
            ->mapK(fn($genre) => [$genre['id'] => $genre['name']]);
    }

    /**
     * Get the genre collection or a specific genre.
     *
     * If no arguments are provided, returns the entire collection. If arguments are provided,
     * returns the specific genre(s) associated with the given key(s).
     *
     * @param mixed ...$args Optional arguments for specific genre retrieval.
     * @return mixed The entire collection or specific genre(s).
     */
    public function get(...$args)
    {
        return func_num_args() === 0 ? $this->genres : $this->genres->get(...$args);
    }

    /**
     * Magic method to get a genre by key.
     *
     * Allows accessing a genre directly via object property syntax.
     *
     * @param string $key The key of the genre to retrieve.
     * @return mixed The genre associated with the given key.
     */
    public function __get($key)
    {
        return $this->genres->get($key);
    }
}


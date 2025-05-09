<?php

namespace App\Lib\Tmdb\Interfaces;

use App\Lib\Tmdb\Model\GenreCollection;

/**
 * Interface for a collection of content items from The Movie Database (TMDB).
 *
 * @package App\Lib\Tmdb\Interfaces
 */
interface ITmdbCollection
{
    /**
     * Adds a collection of content to the collection.
     *
     * @param string $key The key to store the collection of content under.
     * @param array $data The array of content to store.
     * @return self The current instance.
     */
    public function addToCollection(string $key, array $data): self;

    /**
     * Retrieves the GenreCollection instance used by the collection.
     *
     * @return GenreCollection The GenreCollection instance used by the collection.
     */
    public function getGenres(): GenreCollection;

    /**
     * Retrieves the content collection for the given key.
     *
     * @param string $key The key of the content collection to retrieve.
     * @return array The content collection for the given key.
     */
    public function getFromCollection(string $key): array;
}


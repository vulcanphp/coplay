<?php

namespace App\Lib\Tmdb\Model;

use App\Lib\Tmdb\Interfaces\ITmdbCollection;

/**
 * Class TmdbCollection
 *
 * A collection of content items (movies, TV shows, etc.) grouped by a specific key.
 *
 * @package App\Lib\Tmdb\Model
 */
class TmdbCollection implements ITmdbCollection
{
    /**
     * @var array The collection of content items.
     */
    protected array $collection;

    /**
     * TmdbCollection constructor.
     *
     * Initializes the collection with the given genre collection and optional content data.
     *
     * @param GenreCollection $genreCollection The GenreCollection instance to be used.
     * @param array $collect Optional array of content data to initialize the collection.
     */
    public function __construct(protected GenreCollection $genreCollection, array $collect = [])
    {
        // Add each content data to the collection using the specified key.
        foreach ($collect as $key => $data) {
            $this->addToCollection($key, $data);
        }
    }

    /**
     * Adds a collection of content to the collection.
     *
     * @param string $key The key to store the collection of content under.
     * @param array $data The array of content to store.
     * @return self
     */
    public function addToCollection(string $key, array $data): self
    {
        // Map each movie in the data array to a TmdbContent instance
        $this->collection[$key] = array_map(function ($movie) {
            $collect = collect($movie);

            $collect->offsetSet(
                'genres',
                collect($movie['genre_ids'] ?? [])
                    // Map genre IDs to genre names using the genre collection
                    ->mapWithKeys(fn($genre) => [$genre => $this->getGenres()->get($genre)])
                    ->all()
            );

            return new TmdbContent(
                // Convert the movie array into a collection for manipulation
                $collect
                    // Remove the 'genre_ids' key from the movie collection
                    ->except('genre_ids')
                    // Convert the modified collection back to an array
                    ->all()
            );
        }, $data);

        return $this; // Return the current instance for method chaining
    }

    /**
     * Set the GenreCollection instance to be used by the collection.
     *
     * @param GenreCollection $genres The GenreCollection instance to be used.
     * @return self The current instance.
     */
    public function setGenreCollection(GenreCollection $genres): self
    {
        $this->genreCollection = $genres;
        return $this;
    }

    /**
     * Retrieve the GenreCollection instance.
     *
     * @return GenreCollection The current GenreCollection instance used by this collection.
     */
    public function getGenres(): GenreCollection
    {
        return $this->genreCollection;
    }

    /**
     * Retrieves the content collection for the given key.
     *
     * @param string $key The key of the content collection to retrieve.
     * @return array The content collection for the given key.
     */
    public function getFromCollection(string $key): array
    {
        return $this->collection[$key];
    }

    /**
     * Converts the collection of content into an array format.
     *
     * @return array The collection of content items as an array.
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Magic method to retrieve a content collection by key.
     *
     * Allows accessing a content collection directly via object property syntax.
     *
     * @param string $key The key of the content collection to retrieve.
     * @return array|null The content collection associated with the given key, or null if not found.
     */
    public function __get($key)
    {
        return $this->collection[$key] ?? null;
    }

    /**
     * Checks if a content collection exists by key.
     *
     * @param string $key The key of the content collection to check.
     * @return bool True if the content collection exists, false otherwise.
     */
    public function __isset($key)
    {
        return isset($this->collection[$key]);
    }

    /**
     * Removes and returns a content collection by key.
     *
     * The specified content collection is removed from the main collection
     * and returned. If the key does not exist, an empty array is returned.
     *
     * @param string $key The key of the content collection to cut.
     * @return array The removed content collection, or an empty array if not found.
     */
    public function cut(string $key): array
    {
        $section = $this->getFromCollection($key);
        unset($this->collection[$key]);

        return $section;
    }

    /**
     * Magic method to call a function on a content collection by key.
     *
     * Allows accessing a content collection directly via object method syntax.
     * The method will pass the content collection associated with the given key
     * as the first argument to the specified callback function.
     *
     * @param string $name The key of the content collection to call.
     * @param callable $callback The callback function to call.
     * @return mixed The result of the callback function, or the content collection
     *               if no callback is provided.
     */
    public function __call($name, $arguments)
    {
        $callback = array_shift($arguments);

        // If a callback function is provided, call it with the content collection
        // associated with the given key as the first argument.
        if (isset($callback)) {
            array_unshift($arguments, $this->getFromCollection($name));

            return call_user_func_array($callback, $arguments);
        }

        // Otherwise, return the content collection associated with the given key.
        return $this->getFromCollection($name);
    }
}

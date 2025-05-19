<?php

namespace App\Lib\Tmdb\Interfaces;

use ArrayAccess;

/**
 * Interface ITmdbClient
 * @package App\Lib\Tmdb\Interfaces
 */
interface ITmdbClient
{
    /**
     * Get information about a movie or TV show.
     *
     * @param int $id The ID of the movie or TV show.
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     */
    public function getInfo(int $id, array $args = []): array;

    /**
     * Get the TMDb ID of the given IMDb ID.
     *
     * @param string $id The IMDb ID to search for.
     * @return int|null The TMDb ID if found, otherwise null.
     */
    public function getImdbId(string $id): ?int;

    /**
     * Make a request to the TMDB API using the given path and arguments.
     *
     * @param string $path The path to request.
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     */
    public function request(string $path, array $args = []): array;

    /**
     * Search for content using the given arguments.
     *
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     */
    public function search(array $args = []): array;

    /**
     * Discover content using the given arguments.
     *
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     */
    public function find(array $args = []): array;

    /**
     * Make a request to the TMDB API using the given endpoint and parameters.
     *
     * @param string $endpoint The endpoint to request.
     * @param array $params Optional parameters to refine the search.
     * @return ArrayAccess The search results as an array.
     */
    public function send(string $endpoint, array $params = []): ArrayAccess;

    /**
     * Get the genres.
     *
     * @return array The genres as an array.
     */
    public function getGenre(): array;

    /**
     * Get popular content.
     *
     * @return array The popular content as an array.
     */
    public function getPopular(): array;

    /**
     * Get content that is currently playing.
     *
     * @return array The playing content as an array.
     */
    public function getPlaying(): array;

    /**
     * Get top rated content.
     *
     * @return array The top rated content as an array.
     */
    public function getTopRated(): array;

    /**
     * Get the episodes of a TV show.
     *
     * @param int $id The ID of the TV show.
     * @param int $season The season number to get the episodes of.
     * @return array The episodes as an array.
     */
    public function getEpisodes(int $id, int $season = 1): array;
}

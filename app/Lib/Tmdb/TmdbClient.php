<?php

namespace App\Lib\Tmdb;

use App\Lib\Tmdb\Exceptions\TmdbClientException;
use App\Lib\Tmdb\Interfaces\ITmdbClient;
use Spark\Utils\Http;

/**
 * This is a Helper Class to help fetch data from TMDB
 * this class used by Spark\Utils\Http package to perform http request
 * 
 * @reference https://developer.themoviedb.org/reference/intro/getting-started
 * @author Shahin Moyshan <https://github.com/vulcanphp>
 * @version 1.0.0
 */
class TmdbClient implements ITmdbClient
{
    /**
     * The type of content to fetch from TMDB.
     * Can be 'movie', 'tv', or other valid types.
     *
     * @var string|null
     */
    public ?string $type = 'movie';

    /**
     * Construct a new instance.
     *
     * @param string|null $type
     */
    public function __construct(?string $type = null)
    {
        if ($type !== null) {
            $this->setType($type);
        }
    }

    /**
     * Create a new instance.
     *
     * @param string|null $type
     * @return static
     */
    public static function create(...$args): TmdbClient
    {
        return new TmdbClient(...$args);
    }

    /**
     * Set the type of content to search for.
     *
     * @param string $type One of "movie" or "tv".
     * @return $this
     * @throws TmdbClientException
     */
    public function setType(string $type): self
    {
        if (!in_array($type, ['movie', 'tv'])) {
            throw new TmdbClientException('Invalid Type (' . $type . ')');
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get the type of content to search for.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Search for content using the given arguments.
     *
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function search(array $args = []): array
    {
        return $this->send(
            sprintf('/search/%s', $this->type),
            $args
        )['body'];
    }

    /**
     * Discover content using the given arguments.
     *
     * @param array $args Optional parameters to refine the search.
     * @return array The search results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function find(array $args = []): array
    {
        return $this->send(
            sprintf('/discover/%s', $this->type),
            $args
        )['body'];
    }

    /**
     * Find the TMDb ID of the given IMDb ID.
     *
     * @param string $id The IMDb ID to search for.
     * @return int|null The TMDb ID if found, otherwise null.
     */
    public function getImdbId(string $id): ?int
    {
        return $this->send(
            sprintf('/find/%s?external_source=imdb_id', $id)
        )['body']["{$this->type}_results"][0]['id'] ?? null;
    }

    /**
     * Make a request to the TMDB API using the given path and arguments.
     *
     * @param string $path The path of the API endpoint to request.
     * @param array $args Optional query parameters to pass to the request.
     * @return array The response body as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function request(string $path, array $args = []): array
    {
        return $this->send(
            sprintf('/%s', $path),
            $args
        )['body'];
    }

    /**
     * Retrieve a list of alternative titles for the given content.
     *
     * @param int $tmdb The TMDb ID of the content to retrieve alternative titles for.
     * @return array The alternative titles as an array.
     * @see https://developers.themoviedb.org/3/movies/get-alternative-titles
     */
    public function altTitles(int $tmdb): array
    {
        return $this->request("{$this->type}/$tmdb/alternative_titles")['results'] ?? [];
    }

    /**
     * Retrieve a list of genres for the given content type.
     *
     * @return array The list of genres as an array.
     * @see https://developers.themoviedb.org/3/genres/get-genre-list
     */
    public function getGenre(): array
    {
        return $this->request('genre/' . $this->getType() . '/list')['genres'] ?? [];
    }

    /**
     * Retrieve a list of popular content.
     *
     * @return array The popular content results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function getPopular(): array
    {
        return $this->request($this->getType() . '/popular')['results'] ?? [];
    }

    /**
     * Retrieve a list of content that is currently playing.
     *
     * @return array The currently playing content results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function getPlaying(): array
    {
        return $this->request($this->getType() . '/now_playing')['results'] ?? [];
    }

    /**
     * Retrieve a list of content that is the highest rated.
     *
     * @return array The top rated content results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function getTopRated(): array
    {
        return $this->request($this->getType() . '/top_rated')['results'] ?? [];
    }

    /**
     * Retrieve a list of content that is currently on the air.
     *
     * @return array The on the air content results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function getOnTheAir(): array
    {
        return $this->request($this->getType() . '/on_the_air')['results'] ?? [];
    }

    /**
     * Retrieve a list of TV shows that are airing today.
     *
     * @return array The airing today content results as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function getAiringToday(): array
    {
        return $this->request($this->getType() . '/airing_today')['results'] ?? [];
    }

    /**
     * Retrieve a list of trending content.
     *
     * @param string $period One of "day" or "week".
     * @return array The trending content results as an array.
     * @throws TmdbClientException If the request fails.
     * @see https://developers.themoviedb.org/3/trending/get-trending
     */
    public function getTrending($period = 'day'): array
    {
        return $this->request('trending/' . $this->getType() . '/' . $period)['results'] ?? [];
    }

    /**
     * Retrieve a list of trending people.
     *
     * @param string $period One of "day" or "week".
     * @return array The trending people results as an array.
     * @throws TmdbClientException If the request fails.
     * @see https://developers.themoviedb.org/3/trending/get-trending
     */
    public function getTrendingPersons($period = 'day'): array
    {
        return $this->request("trending/person/$period")['results'] ?? [];
    }

    /**
     * Retrieve a list of videos that are recommended to the given video.
     *
     * @param int $id The ID of the video to retrieve recommended videos for.
     * @return array The recommended videos results as an array.
     * @throws TmdbClientException If the request fails.
     * @see https://developers.themoviedb.org/3/movies/get-similar-movies
     */
    public function getRecommendedVideos(int $id): array
    {
        return $this->request($this->getType() . '/' . $id . '/recommendations')['results'] ?? [];
    }

    /**
     * Retrieve the information for a given content ID.
     *
     * @param int $id The TMDb ID of the content to retrieve information for.
     * @param array $args The optional arguments to pass with the request.
     * @return array The information for the given content ID as an array.
     * @throws TmdbClientException If the request fails.
     * @see https://developers.themoviedb.org/3/movies/get-movie-details
     */
    public function getInfo(int $id, array $args = []): array
    {
        return $this->send(
            sprintf('/%s/%s', $this->type, $id),
            $args
        )['body'];
    }

    /**
     * Retrieve the episodes for a given content ID and season.
     *
     * @param int $id The TMDb ID of the content to retrieve episodes for.
     * @param int $season The season number of the episodes to retrieve.
     * @return array The episodes for the given content ID and season as an array.
     * @throws TmdbClientException If the request fails.
     * @see https://developers.themoviedb.org/3/tv/episodes/get-tv-episode-details
     */
    public function getEpisodes(int $id, int $season = 1): array
    {
        return $this->send(
            sprintf('/%s/%s/season/%s', $this->type, $id, $season)
        )['body']['episodes'] ?? [];
    }

    /**
     * Send a request to the TMDB API using the given endpoint and parameters.
     *
     * @param string $endpoint The endpoint to request.
     * @param array $params The optional parameters to pass with the request.
     * @return array The response as an array with the following keys:
     *     - status: The HTTP status code of the response.
     *     - body: The response body as an array.
     * @throws TmdbClientException If the request fails.
     */
    public function send(string $endpoint, array $params = []): array
    {
        $resp = get(Http::class)
            // Set the Accept header to JSON
            ->header('Accept', 'application/json')
            // Set the Content-Type header to JSON
            ->header('Content-Type', 'application/json;charset=utf-8')
            // Set the Authorization header with the API key
            ->header('Authorization', sprintf('Bearer %s', config('TMDB_API_KEY')))
            // Make the request to the TMDB API
            ->get('http://api.themoviedb.org/3/' . trim($endpoint, '/'), $params);

        // If the request failed, throw an exception
        if ($resp['status'] !== 200) {
            throw new TmdbClientException('Tmdb Client Error: ' . $resp['body'], $resp['status']);
        }

        // Decode the response body as JSON
        $resp['body'] = json_decode($resp['body'], true);

        // Return the response
        return $resp;
    }
}

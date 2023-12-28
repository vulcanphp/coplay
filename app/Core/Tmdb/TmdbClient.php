<?php

namespace App\Core\Tmdb;

use App\Core\Configurator;
use App\Core\Tmdb\Exceptions\TmdbClientException;
use App\Core\Tmdb\Interfaces\ITmdbClient;
use VulcanPhp\EasyCurl\EasyCurl;
use VulcanPhp\EasyCurl\EasyCurlResponse;

/**
 * This is a Helper Class to help fetch data from TMDB
 * this class used by VulcanPhp/EasyCurl package to perform http request
 * and config/app.php to store TMDB api keys
 * 
 * @see https://developer.themoviedb.org/reference/intro/getting-started
 * @version 1.0.0
 * @author Shahin Moyshan
 * @link https://github.com/vulcanphp
 * @package VulcanPhp/TMDB
 */
class TmdbClient implements ITmdbClient
{
    const API_ENDPOINT      = 'http://api.themoviedb.org/3';
    public  ?string $type   = 'movie';

    public function __construct(?string $type = null)
    {
        if ($type !== null) {
            $this->setType($type);
        }
    }

    public static function create(...$args): TmdbClient
    {
        return new TmdbClient(...$args);
    }

    public function setType(string $type): self
    {
        if (!in_array($type, ['movie', 'tv'])) {
            throw new TmdbClientException('Invalid Type (' . $type . ')');
        }

        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function search(array $args = []): array
    {
        return $this->send(
            sprintf('%s/search/%s', self::API_ENDPOINT, $this->type),
            $args
        )->getJson();
    }

    public function find(array $args = []): array
    {
        return $this->send(
            sprintf('%s/discover/%s', self::API_ENDPOINT, $this->type),
            $args
        )->getJson();
    }

    public function getImdbId(string $id): ?int
    {
        return $this->send(
            sprintf('%s/find/%s?external_source=imdb_id', self::API_ENDPOINT, $id)
        )->getJson()[$this->type . '_results'][0]['id'] ?? null;
    }

    public function request(string $path, array $args = []): array
    {
        return $this->send(
            sprintf('%s/%s', self::API_ENDPOINT, $path),
            $args
        )->getJson();
    }

    public function altTitles(int $tmdb): array
    {
        return $this->request($this->getType() . '/' . $tmdb . '/alternative_titles')['results'] ?? [];
    }

    public function send(...$args): EasyCurlResponse
    {
        $resp = EasyCurl::setHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json;charset=utf-8',
            'Authorization' => sprintf('Bearer %s', Configurator::$instance->get('tmdb'))
        ])->get(...$args);

        if ($resp->getStatus() != 200) {
            throw new TmdbClientException('Tmdb Client Error: ' . $resp->getBody(), $resp->getStatus());
        }

        return $resp;
    }

    public function getGenre(): array
    {
        return $this->request('genre/' . $this->getType() . '/list')['genres'] ?? [];
    }

    public function getPopular(): array
    {
        return $this->request($this->getType() . '/popular')['results'] ?? [];
    }

    public function getPlaying(): array
    {
        return $this->request($this->getType() . '/now_playing')['results'] ?? [];
    }

    public function getTopRated(): array
    {
        return $this->request($this->getType() . '/top_rated')['results'] ?? [];
    }

    public function getOnTheAir(): array
    {
        return $this->request($this->getType() . '/on_the_air')['results'] ?? [];
    }

    public function getAiringToday(): array
    {
        return $this->request($this->getType() . '/airing_today')['results'] ?? [];
    }

    public function getTrending($period = 'day'): array
    {
        return $this->request('trending/' . $this->getType() . '/' . $period)['results'] ?? [];
    }

    public function getTrendingPersons($period = 'day'): array
    {
        return $this->request('trending/person/' . $period)['results'] ?? [];
    }

    public function getRelatedVideos(int $id): array
    {
        return $this->request($this->getType() . '/' . $id . '/recommendations')['results'] ?? [];
    }

    // $args[] = ['append_to_response' => 'videos,credits,external_ids']
    public function getInfo(int $id,  array $args = []): array
    {
        return $this->send(
            sprintf('%s/%s/%s', self::API_ENDPOINT, $this->type, $id),
            $args
        )->getJson();
    }

    public function getEpisodes(int $id,  int $season = 1): array
    {
        return $this->send(
            sprintf('%s/%s/%s/season/%s', self::API_ENDPOINT, $this->type, $id, $season)
        )->getJson()['episodes'] ?? [];
    }
}

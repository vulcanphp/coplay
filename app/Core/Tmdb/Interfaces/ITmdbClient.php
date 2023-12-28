<?php

namespace App\Core\Tmdb\Interfaces;

use VulcanPhp\EasyCurl\EasyCurlResponse;

interface ITmdbClient
{
    public function getInfo(int $id,  array $args = []): array;

    public function getImdbId(string $id): ?int;

    public function request(string $path, array $args = []): array;

    public function search(array $args = []): array;

    public function find(array $args = []): array;

    public function send(...$args): EasyCurlResponse;

    public function getGenre(): array;

    public function getPopular(): array;

    public function getPlaying(): array;

    public function getTopRated(): array;

    public function getEpisodes(int $id,  int $season = 1): array;
}

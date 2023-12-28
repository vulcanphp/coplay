<?php

namespace App\Core\Tmdb\Interfaces;

use App\Core\Tmdb\Model\GenreCollection;

interface ITmdbCollection
{
    public function addToCollection(string $key, array $data): self;

    public function getGenres(): GenreCollection;

    public function getFromCollection(string $key): array;
}

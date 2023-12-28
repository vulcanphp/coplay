<?php

namespace App\Core\Tmdb\Model;

use App\Core\Tmdb\Interfaces\ITmdbCollection;

class TmdbCollection implements ITmdbCollection
{
    protected array $collection;

    public function __construct(protected GenreCollection $genreCollection, array $collect = [])
    {
        foreach ($collect as $key => $data) {
            $this->addToCollection($key, $data);
        }
    }

    public function addToCollection(string $key, array $data): self
    {
        $this->collection[$key] = array_map(fn ($movie) => new TmdbContent(
            collect($movie)
                ->set(
                    'genres',
                    collect($movie['genre_ids'] ?? [])
                        ->mapWithKeys(fn ($genre) => [$genre => $this->getGenres()->get($genre)])
                        ->all()
                )
                ->remove('genre_ids')
                ->all()
        ), $data);

        return $this;
    }

    public function setGenreCollection(GenreCollection $genres): self
    {
        $this->genreCollection = $genres;
        return $this;
    }

    public function getGenres(): GenreCollection
    {
        return $this->genreCollection;
    }

    public function getFromCollection(string $key): array
    {
        return $this->collection[$key];
    }

    public function toArray(): array
    {
        return $this->collection;
    }

    public function __get($key)
    {
        return $this->collection[$key] ?? null;
    }

    public function __isset($key)
    {
        return isset($this->collection[$key]);
    }

    public function cut(string $key): array
    {
        $section = $this->getFromCollection($key);
        unset($this->collection[$key]);

        return $section;
    }

    public function __call($name, $arguments)
    {
        $callback = array_shift($arguments);

        if (isset($callback)) {
            array_unshift($arguments, $this->getFromCollection($name));

            return call_user_func($callback, ...$arguments);
        }

        return $this->getFromCollection($name);
    }
}

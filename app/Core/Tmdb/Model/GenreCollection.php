<?php

namespace App\Core\Tmdb\Model;

class GenreCollection
{
    public function __construct(protected $genres)
    {
        $this->genres  = collect($genres)
            ->mapWithKeys(fn ($genre) => [$genre['id'] => $genre['name']]);
    }

    public function get(...$args)
    {
        return func_num_args() == 0 ? $this->genres : $this->genres->get(...$args);
    }

    public function __get($key)
    {
        return $this->genres->get($key);
    }
}

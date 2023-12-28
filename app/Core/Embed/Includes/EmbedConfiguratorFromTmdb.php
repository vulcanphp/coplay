<?php

namespace App\Core\Embed\Includes;

use VulcanPhp\Core\Helpers\Str;
use App\Core\Tmdb\Model\TmdbContent;

class EmbedConfiguratorFromTmdb extends EmbedConfigurator
{
    public function __construct(protected TmdbContent $content)
    {
        parent::__construct([
            'tmdb' => $content->id,
            'imdb' => $content->imdb_id ?? ($content->external_ids['imdb_id'] ?? null),
            'type' => isset($content->title) && !isset($content->name) ? 'movie' : 'tv',
            'title' => $content->title ?? $content->name,
            'slug' => Str::slugif($content->title ?? $content->name),
            'original_title' => $content->original_title ?? ($content->original_name ?? ''),
            'original_slug' => Str::slugif($content->original_title ?? ($content->original_name ?? '')),
            'year' => date('Y', strtotime($content->release_date ?? ($content->first_air_date ?? ''))),
            'date' => $content->release_date ?? ($content->first_air_date ?? ''),
            'season' => input('season'),
            'episode' => input('episode'),
            'genres' => $content->genres ?? [],
            'countries' => isset($content->origin_country) && !empty($content->origin_country)
                ? $content->origin_country
                :  array_column($content->production_countries ?? [], 'iso_3166_1'),
        ]);
    }

    public function getContent(): TmdbContent
    {
        return $this->content;
    }
}

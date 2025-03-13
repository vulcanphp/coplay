<?php

namespace Lib\Embed\Includes;

use Lib\Tmdb\Model\TmdbContent;

/**
 * Class EmbedConfiguratorFromTmdb
 * An EmbedConfigurator that uses a TmdbContent instance as input
 *
 * @package Lib\Embed\Includes
 */
class EmbedConfiguratorFromTmdb extends EmbedConfigurator
{
    protected TmdbContent $content;

    /**
     * Constructs a new EmbedConfiguratorFromTmdb instance from a TmdbContent instance
     *
     * @param TmdbContent $content The TmdbContent instance to use as input
     */
    public function __construct(TmdbContent $content)
    {
        // Get the season and episode number from the query string
        $input = input(['season', 'episode']);

        // Set the input data
        parent::__construct([
            // The Tmdb ID
            'tmdb' => $content->id,
            // The Imdb ID
            'imdb' => $content->imdb_id ?? ($content->external_ids['imdb_id'] ?? null),
            // The type of the content
            'type' => isset($content->title) && !isset($content->name) ? 'movie' : 'tv',
            // The title of the content
            'title' => $content->title ?? $content->name,
            // The slug of the content
            'slug' => slugify($content->title ?? $content->name),
            // The original title of the content
            'original_title' => $content->original_title ?? ($content->original_name ?? ''),
            // The slug of the original title
            'original_slug' => slugify($content->original_title ?? ($content->original_name ?? '')),
            // The year of the content
            'year' => date('Y', strtotime($content->release_date ?? ($content->first_air_date ?? ''))),
            // The date of the content
            'date' => $content->release_date ?? ($content->first_air_date ?? ''),
            // The season of the content
            'season' => $input->number('season'),
            // The episode of the content
            'episode' => $input->number('episode'),
            // The genres of the content
            'genres' => $content->genres ?? [],
            // The countries of the content
            'countries' => isset($content->origin_country) && !empty($content->origin_country)
                ? $content->origin_country
                : array_column($content->production_countries ?? [], 'iso_3166_1'),
        ]);
    }

    /**
     * Gets the TmdbContent instance used to construct this EmbedConfiguratorFromTmdb
     *
     * @return TmdbContent The TmdbContent instance used to construct this class
     */
    public function getContent(): TmdbContent
    {
        return $this->content;
    }
}


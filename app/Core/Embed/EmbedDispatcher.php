<?php

namespace App\Core\Embed;

use App\Models\Links;
use App\Core\Embed\Embed;
use App\Core\Configurator;
use App\Core\Tmdb\TmdbClient;
use VulcanPhp\Core\Helpers\Arr;
use VulcanPhp\Core\Helpers\Str;
use App\Core\Tmdb\Model\TmdbContent;
use App\Core\Embed\Includes\EmbedCrawler;
use App\Core\Embed\Includes\EmbedFallback;
use App\Core\Embed\Interfaces\IEmbedController;
use App\Core\Tmdb\Exceptions\TmdbClientException;
use App\Core\Embed\Includes\EmbedConfiguratorFromTmdb;

class EmbedDispatcher implements IEmbedController
{
    protected Configurator $config;

    public function __construct(protected string $type, protected int $tmdb)
    {
        $this->config = Configurator::$instance;
    }

    public static function create(...$args): EmbedDispatcher
    {
        return new EmbedDispatcher(...$args);
    }

    public function dispatch(): string
    {
        $client = TmdbClient::create($this->type);
        $fallback = new EmbedFallback();

        try {
            $video = new TmdbContent($client->getInfo($this->tmdb, ['append_to_response' => 'external_ids,alternative_titles']));
        } catch (TmdbClientException) {
            return $fallback->trigger();
        }

        $embed = new Embed(
            new EmbedConfiguratorFromTmdb($video)
        );

        $embed->setCallbackUrl(url()->getPath());
        $fallback->setConfigurator($embed->getConfig());

        if (!empty(trim(input('play', '')))) {
            $callback = new EmbedCrawler($embed->getConfig(), $fallback);

            return $callback->resolve(trim(input('play')));
        }

        if ($this->type == 'tv') {

            $video->current_episode = $video->next_episode_to_air['episode_number'] ?? null;

            // get season number from client or set latest
            $video->seasons = collect($video->seasons)
                ->filter(fn ($se) => ($se['season_number'] ?? 0) > 0 && isset($se['air_date']) && strtotime($se['air_date']) <= time())
                ->map(function ($se) {
                    $se['name'] = str_replace(['"', "'"], '', $se['name']);;
                    return $se;
                })
                ->only(['name', 'air_date', 'season_number', 'poster_path'])
                ->all();

            if (!empty($video->seasons)) {
                $video->number_of_seasons = max(array_column($video->seasons, 'season_number'));
                $video->season = input('season', $video->number_of_seasons);
                $video->seasons = array_reverse($video->seasons);

                try {
                    // fetch episodes form season
                    $video->episodes = collect($client->getEpisodes($this->tmdb, $video->season));
                } catch (TmdbClientException $e) {
                    return $fallback->trigger();
                }

                $customLinks = null;
                if ($this->config->is('links')) {
                    $customLinks = Links::where(['tmdb' => $video->id, 'season' => $video->season])->get();
                }

                $video->episodes = $video->episodes->filter(fn ($ep) => ($ep['season_number'] ?? 0) > 0 && ($ep['episode_number'] ?? 0) > 0 && (!isset($ep['air_date']) || (isset($ep['air_date']) && strtotime($ep['air_date']) <= time())))
                    ->filter(fn ($ep) => !isset($video->current_episode) || (isset($video->current_episode) && $ep['episode_number'] <= $video->current_episode))
                    ->map(function ($episode) use ($embed, $customLinks) {

                        $links = [];

                        if (isset($customLinks)) {
                            foreach ($customLinks->select(function ($link) use ($episode) {
                                if ($link->season == $episode['season_number'] && $link->episode == $episode['episode_number'] && !empty($link->link)) {
                                    return $link;
                                }
                                return null;
                            })
                                ->filter()
                                ->map(function ($link) {
                                    return [
                                        'source' => $link->link,
                                        'name' => $link->server,
                                        'quality' => 'Multi Quality',
                                        'id' => Str::slug($link->server),
                                    ];
                                })
                                ->all() as $link) {
                                array_push($links, $link);
                            }
                        }

                        if ($this->config->is('embeds')) {
                            $embed->getConfig()
                                ->push('season', $episode['season_number'])
                                ->push('episode', $episode['episode_number']);

                            $links = array_merge($links, $embed->getLinks());
                        }

                        if (empty($links)) {
                            return null;
                        }

                        $episode['name'] = str_replace(['"', "'"], '', $episode['name']);
                        $episode['links'] = $links;

                        return $episode;
                    })
                    ->filter()
                    ->only(['air_date', 'episode_number', 'season_number', 'name', 'still_path', 'links'])
                    ->all();

                if (!empty($video->episodes)) {
                    $video->episodes = array_reverse($video->episodes);

                    // get episode number from client or set latest
                    $video->episode = input('episode', Arr::first($video->episodes)['episode_number']);
                    $video->links = collect($video->episodes)->find(['episode_number' => intval($video->episode)])['links'] ?? null;
                }
            }
        } else {

            $links = [];

            if ($this->config->is('links')) {
                foreach (Links::where(['tmdb' => $video->id, 'season' => 1, 'episode' => 1])
                    ->get()
                    ->filter(fn ($link) => !empty($link->link))
                    ->map(function ($link) {
                        return [
                            'source' => $link->link,
                            'name' => $link->server,
                            'quality' => 'Multi Quality',
                            'id' => Str::slug($link->server),
                        ];
                    })
                    ->all() as $link) {
                    array_push($links, $link);
                }

                if ($this->config->is('embeds')) {
                    $links = array_merge($links, $embed->getLinks());
                }
            }

            $video->links = $links;
        }

        if (isset($video->links) && !empty($video->links)) {
            return view('embed.index', ['video' => $video]);
        }

        return $fallback->trigger();
    }

    public function checkOrigin(): self
    {
        if (
            !$this->config->is('api')
            && request()->header('http-sec-fetch-site') == 'cross-site'
        ) {
            abort(404);
        }

        return $this;
    }
}

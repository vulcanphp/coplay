<?php

namespace App\Http\Controllers;

use App\Core\Configurator;
use App\Core\Embed\EmbedDispatcher;
use App\Core\Tmdb\Exceptions\TmdbClientException;
use App\Core\Tmdb\Model\GenreCollection;
use App\Core\Tmdb\Model\TmdbCollection;
use App\Core\Tmdb\Model\TmdbContent;
use App\Core\Tmdb\TmdbClient;
use VulcanPhp\Core\Helpers\Str;

class Home
{
    protected Configurator $config;

    public function __construct()
    {
        $this->config = Configurator::$instance;
    }

    public function home()
    {
        $tmdb   = TmdbClient::create('movie');
        $cache  = cache('tmdb.home');

        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('movie.genres', fn () => $tmdb->getGenre())
            ),
            [
                'trending-movie' => $cache->load('movie.trending', fn () => $tmdb->getTrending(), '12 hours'),
                'people' => $cache->load('people', fn () => $tmdb->getTrendingPersons(), '12 hours'),
            ]
        );

        $tmdb->setType('tv');

        $viewModel->setGenreCollection(
            new GenreCollection(
                $cache->load('tv.genres', fn () => $tmdb->getGenre())
            ),
        )->addToCollection('trending-tv-series', $cache->load('tv.trending', fn () => $tmdb->getTrending(), '12 hours'));

        return view('videos', ['people' => $viewModel->cut('people'), 'collection' => $viewModel->toArray()]);
    }


    public function movie($slug = null)
    {
        $tmdb   = TmdbClient::create('movie');
        $cache  = cache('tmdb.movie');

        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('genres', fn () => $tmdb->getGenre())
            ),
        );

        if ($slug !== null) {
            $id = substr($slug, strrpos($slug, '-') + 1);
            $viewModel->addToCollection('similar', TmdbClient::create('movie')->getRelatedVideos($id));

            return view('video', [
                'video' => $this->findOrFail($id, 'movie'),
                'similar' => $viewModel->cut('similar')
            ]);
        }


        $viewModel
            ->addToCollection('popular', $cache->load('popular', fn () => $tmdb->getPopular(), '12 hours'))
            ->addToCollection('now-playing', $cache->load('playing', fn () => $tmdb->getPlaying(), '12 hours'))
            ->addToCollection('toprated', $cache->load('toprated', fn () => $tmdb->getTopRated(), '12 hours'));

        return view(
            'videos',
            [
                'top' => $viewModel->cut('toprated'),
                'collection' => $viewModel->toArray(),
            ]
        );
    }

    public function tv($slug = null)
    {
        $tmdb   = TmdbClient::create('tv');
        $cache  = cache('tmdb.tv');

        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('genres', fn () => $tmdb->getGenre())
            )
        );

        if ($slug !== null) {
            $id = substr($slug, strrpos($slug, '-') + 1);

            $viewModel->addToCollection('similar', TmdbClient::create('tv')->getRelatedVideos($id));

            return view('video', [
                'video' => $this->findOrFail($id, 'tv'),
                'similar' => $viewModel->cut('similar')
            ]);
        }

        $viewModel
            ->addToCollection('airing-today', $cache->load('airingtoday', fn () => $tmdb->getAiringToday(), '12 hours'))
            ->addToCollection('popular', $cache->load('popular', fn () => $tmdb->getPopular(), '12 hours'))
            ->addToCollection('this-week', $cache->load('ontheair', fn () => $tmdb->getOnTheAir(), '12 hours'))
            ->addToCollection('toprated', $cache->load('toprated', fn () => $tmdb->getTopRated(), '12 hours'));

        return view('videos', [
            'top' => $viewModel->cut('toprated'),
            'collection' => $viewModel->toArray(),
        ]);
    }

    public function embed($type, $id)
    {
        return EmbedDispatcher::create($type, $id)
            ->checkOrigin()
            ->dispatch();
    }

    protected function findOrFail($id, string $type = 'movie'): TmdbContent
    {
        try {
            return new TmdbContent(
                TmdbClient::create($type)
                    ->getInfo($id, ['append_to_response' => 'videos,images,credits'])
            );
        } catch (TmdbClientException) {
            abort(404);
        }
    }

    public function watchlater()
    {
        return view('watchlater');
    }

    public function api()
    {
        if (!$this->config->is('api')) {
            abort(404);
        }

        return view('api');
    }

    public function search($keyword)
    {
        $results = [];
        $keyword = trim(strval($keyword ?? ''));

        if (strlen($keyword) >= 2) {
            // check if keyword endswith year
            if (preg_match('/\d{4}$/', $keyword, $match)) {
                $year = $match[0] ?? null;
                $keyword = trim(preg_replace('/\d{4}$/', '', $keyword));
            } else {
                $year = null;
            }

            try {
                $results = collect(TmdbClient::create()->request('search/multi', ['query' => $keyword])['results'] ?? [])
                    ->filter(fn ($vid) => (strtotime($vid['release_date'] ?? ($vid['first_air_date'] ?? '')) <= time() && ($vid['vote_count'] ?? 0) > 0))
                    ->map(function ($vid) use ($keyword, $year) {
                        $priority = 0;

                        // match with year
                        if (isset($year) && $year == date('Y', strtotime($vid['release_date'] ?? $vid['first_air_date']))) {
                            $priority = 1000;
                        }

                        // set priority for matching title
                        $priority += ((Str::slugif(($vid['title'] ?? $vid['name'])) == Str::slugif($keyword)) ? 500 : 0);
                        $priority += (like_match("%$keyword%", ($vid['title'] ?? $vid['name'])) ? 250 : 0);

                        // set priority for popularity
                        $priority += $vid['popularity'];

                        // set priority for available post data
                        foreach (['poster_path', 'backdrop_path', 'overview', 'genre_ids', 'origin_country', 'original_language'] as $find) {
                            $priority += (isset($vid[$find]) && !empty($vid[$find]) ? 10 : 0);
                        }

                        $vid['priority'] = $priority;

                        return $vid;
                    })
                    ->multisort('priority', true)
                    ->all();
            } catch (TmdbClientException $e) {
                return 'Internal Server Error';
            }
        } else {
            return '';
        }

        return view('ajax.search', ['results' => $results, 'keyword' => $keyword]);
    }
}

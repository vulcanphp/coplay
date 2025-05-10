<?php

namespace App\Http\Controllers;

use App\Lib\Embed\EmbedDispatcher;
use App\Lib\Tmdb\Exceptions\TmdbClientException;
use App\Lib\Tmdb\Model\GenreCollection;
use App\Lib\Tmdb\Model\TmdbCollection;
use App\Lib\Tmdb\Model\TmdbContent;
use App\Lib\Tmdb\TmdbClient;
use Spark\Http\Request;
use Spark\Http\Response;
use Spark\Support\Str;
use Throwable;

/**
 * The home page of the website.
 *
 * This class contains the logic for rendering the home page of the website.
 * The home page will display the trending movies, TV shows, and people.
 * 
 * @package Controllers
 */
class HomeController
{
    /**
     * The home page of the website.
     *
     * This function is the entry point of the website. It will render the home page
     * with the trending movies, TV shows, and people.
     *
     * @return Response The response object containing the rendered template.
     */
    public function index()
    {
        $tmdb = TmdbClient::create('movie');
        $cache = cache('tmdb.home');

        // Get the movie genres
        $movieGenres = new GenreCollection(
            $cache->load('movie.genres', fn() => $tmdb->getGenre())
        );

        // Get trending movies
        $trendingMovies = $cache->load('movie.trending', fn() => $tmdb->getTrending(), '12 hours');

        // Get trending people
        $trendingPeople = $cache->load('people', fn() => $tmdb->getTrendingPersons(), '12 hours');

        // Create a view model
        $viewModel = new TmdbCollection(
            $movieGenres,
            [
                'trending-movie' => $trendingMovies,
                'people' => $trendingPeople,
            ]
        );

        // Set the genre collection for TV shows
        $tmdb->setType('tv');
        $tvGenres = new GenreCollection(
            $cache->load('tv.genres', fn() => $tmdb->getGenre())
        );

        $viewModel->setGenreCollection($tvGenres);

        // Get trending TV shows
        $trendingTvShows = $cache->load('tv.trending', fn() => $tmdb->getTrending(), '12 hours');

        // Add trending TV shows to the view model
        $viewModel->addToCollection('trending-tv-series', $trendingTvShows);

        // Get items for the slider
        $sliderItems = collect(
            array_merge(
                array_slice($viewModel->getFromCollection('trending-movie'), 0, 10),
                array_slice($viewModel->getFromCollection('trending-tv-series'), 0, 10)
            )
        )
            ->map(function ($item) {
                $item = $item->toArray();
                $item['time'] = strtotime(strval($item['release_date'] ?? $item['first_air_date']));
                return $item;
            })
            ->sortBy(['time' => 'desc'])
            ->sortBy(['vote_count' => 'desc'])
            ->sortBy(['vote_average' => 'desc'])
            ->slice(0, 10)
            ->except(['time'])
            ->all();

        // Return the view
        return fireline('videos', [
            'people' => $viewModel->cut('people'),
            'collection' => $viewModel->toArray(),
            'sliderItems' => $sliderItems
        ]);
    }


    /**
     * Display the movie page.
     *
     * This method will render the movie page with the popular movies,
     * movies that are now playing, and the top rated movies.
     *
     * If a slug is provided, it will render a single movie page with
     * similar movies.
     *
     * @param string|null $slug The slug of the movie.
     *
     * @return Response The rendered template.
     */
    public function movie(?string $slug = null)
    {
        $tmdb = TmdbClient::create('movie');
        $cache = cache('tmdb.movie');

        // Create a view model with the movie genres
        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('genres', fn() => $tmdb->getGenre())
            ),
        );

        // If a slug is provided, render a single movie page
        if ($slug !== null) {
            $id = Str::afterLast($slug, '-');
            $movie = $this->findOrFail($id, 'movie');
            // Add similar movies to the view model
            $viewModel->addToCollection(
                'similar',
                TmdbClient::create('movie')->getRecommendedVideos($id)
            );

            // Render the video template with the movie and similar movies
            return fireline('video', [
                'video' => $movie,
                'similar' => $viewModel->cut('similar')
            ]);
        }

        // Add popular, now playing, and top rated movies to the view model
        $viewModel
            ->addToCollection('popular', $cache->load('popular', fn() => $tmdb->getPopular(), '12 hours'))
            ->addToCollection('now-playing', $cache->load('playing', fn() => $tmdb->getPlaying(), '12 hours'))
            ->addToCollection('toprated', $cache->load('toprated', fn() => $tmdb->getTopRated(), '12 hours'));

        // Render the videos template with the top rated movies and the view model
        return fireline('videos', [
            'top' => $viewModel->cut('toprated'),
            'collection' => $viewModel->toArray(),
        ]);
    }

    /**
     * Display the TV shows page.
     *
     * This method will render the TV shows page with the TV shows airing today,
     * popular TV shows, TV shows airing this week, and top rated TV shows.
     *
     * If a slug is provided, it will render a single TV show page with
     * similar TV shows.
     *
     * @param string|null $slug The slug of the TV show.
     *
     * @return Response The rendered template.
     */
    public function tv(?string $slug = null)
    {
        // Create a TMDB client for TV shows
        $tmdb = TmdbClient::create('tv');
        // Load cached data for TV shows
        $cache = cache('tmdb.tv');

        // Initialize the view model with TV genres
        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('genres', fn() => $tmdb->getGenre())
            )
        );

        // If a slug is provided, render a specific TV show page
        if ($slug !== null) {
            $id = Str::afterLast($slug, '-');
            $tv = $this->findOrFail($id, 'tv');
            // Add similar TV shows to the view model
            $viewModel->addToCollection(
                'similar',
                TmdbClient::create('tv')->getRecommendedVideos($id)
            );

            // Render the video template with the TV show and similar TV shows
            return fireline('video', [
                'video' => $tv,
                'similar' => $viewModel->cut('similar')
            ]);
        }

        // Add TV shows airing today, popular, airing this week, and top rated to the view model
        $viewModel
            ->addToCollection('airing-today', $cache->load('airingtoday', fn() => $tmdb->getAiringToday(), '12 hours'))
            ->addToCollection('popular', $cache->load('popular', fn() => $tmdb->getPopular(), '12 hours'))
            ->addToCollection('this-week', $cache->load('ontheair', fn() => $tmdb->getOnTheAir(), '12 hours'))
            ->addToCollection('toprated', $cache->load('toprated', fn() => $tmdb->getTopRated(), '12 hours'));

        // Render the videos template with the top rated TV shows and the view model
        return fireline('videos', [
            'top' => $viewModel->cut('toprated'),
            'collection' => $viewModel->toArray(),
        ]);
    }

    /**
     * Render the embed page for a given video content ID.
     *
     * The embed page is responsible for rendering the video content in an iframe.
     * The iframe src is generated by the EmbedDispatcher class.
     *
     * The method checks the request origin and dispatches the request to the
     * EmbedDispatcher class.
     *
     * @param Request $request The incoming HTTP request.
     * @param string $type The type of content (e.g. "movie", "tv").
     * @param int $id The TMDB ID of the content.
     * @return Response The HTTP response containing the embedded video content.
     */
    public function embed(Request $request, $type, $id)
    {
        return EmbedDispatcher::create($type, $id)
            ->checkOrigin($request)
            ->dispatch($request);
    }

    /**
     * Find the content by ID and type or fail.
     *
     * This method attempts to retrieve a content item from TMDB by its ID
     * and type (e.g., movie or TV show). If the content is not found, it
     * triggers a 404 page not found error.
     *
     * @param mixed $id The ID of the content to retrieve.
     * @param string $type The type of content, default is 'movie'.
     * @return TmdbContent The content retrieved from TMDB.
     */
    protected function findOrFail($id, string $type = 'movie'): TmdbContent
    {
        $content = null;
        try {
            // Attempt to retrieve the content from TMDB API
            $content = TmdbClient::create($type)
                ->getInfo($id, ['append_to_response' => 'videos,images,credits']);
        } catch (Throwable $e) {
            // Log the exception or handle it as needed
        }

        if (empty($content)) {
            // Trigger a 404 error if content is not found
            abort(404);
        }

        // Return the content wrapped in a TmdbContent object
        return new TmdbContent($content);
    }

    /**
     * Renders the watchlater page containing the watchlist
     * of the user.
     *
     * @return Response The rendered watchlater page.
     */
    public function watchlater()
    {
        return fireline('watchlater');
    }

    /**
     * Render the API page if the API feature is enabled.
     *
     * If the API feature is not enabled, it will trigger a 404 page not found error.
     *
     * @return Response The rendered API page.
     */
    public function api()
    {
        if (!is_feature_enabled('api')) {
            // Trigger a 404 error if API feature is not enabled
            abort(404);
        }

        // Render the API page if the API feature is enabled
        return fireline('api');
    }

    /**
     * Renders the search result page for the given keyword.
     *
     * It uses the TmdbClient to search for movies and TV series
     * with the given keyword and returns the result as a JSON response.
     *
     * It also sets a priority for each result based on the following criteria:
     *
     * - Exact match with the title
     * - Partial match with the title
     * - Popularity
     * - Availability of poster, backdrop, overview, genres, origin country and original language
     *
     * The results are sorted by the priority and then by the popularity in descending order.
     *
     * If the keyword is empty, it returns an empty response.
     */
    public function search()
    {
        $results = [];
        $keyword = trim(request('keyword', '')); // get the search keyword

        if (strlen($keyword) >= 2) {
            // check if keyword endswith year
            // if so, remove the year from the keyword
            if (preg_match('/\d{4}$/', $keyword, $match)) {
                $year = $match[0] ?? null;
                $keyword = trim(preg_replace('/\d{4}$/', '', $keyword));
            } else {
                $year = null;
            }

            try {
                $results = collect(
                    TmdbClient::create()
                        ->request('search/multi', ['query' => $keyword])['results'] ?? []
                )
                    ->filter(
                        fn($vid) => (strtotime($vid['release_date'] ?? ($vid['first_air_date'] ?? '')) <= time() && ($vid['vote_count'] ?? 0) > 0)
                    )
                    ->map(function ($vid) use ($keyword, $year) {
                        $priority = 0;

                        // match with year
                        if (isset($year) && $year == date('Y', strtotime($vid['release_date'] ?? $vid['first_air_date']))) {
                            $priority = 1000;
                        }

                        // set priority for matching title
                        $priority += (Str::slug($vid['title'] ?? $vid['name']) == Str::slug($keyword)) ? 500 : 0;
                        $priority += stripos($vid['title'] ?? $vid['name'], $keyword) ? 250 : 0;

                        // set priority for popularity
                        $priority += $vid['popularity'];

                        // set priority for available post data
                        foreach (['poster_path', 'backdrop_path', 'overview', 'genre_ids', 'origin_country', 'original_language'] as $find) {
                            $priority += isset($vid[$find]) && !empty($vid[$find]) ? 10 : 0;
                        }

                        $vid['priority'] = $priority;

                        return $vid;
                    })
                    ->sortByDesc('priority')
                    ->values()
                    ->all();
            } catch (TmdbClientException $e) {
                // Log the exception or handle it as needed
                // Return an error response
                return response('Internal Server Error, Please Try again later.');
            }
        } else {
            // If the keyword is empty, return an empty response
            return response('');
        }

        // Render the search result page
        return fireline('ajax/search', ['results' => $results, 'keyword' => $keyword]);
    }
}

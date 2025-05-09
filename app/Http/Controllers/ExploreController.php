<?php

namespace App\Http\Controllers;

use App\Lib\Tmdb\Model\GenreCollection;
use App\Lib\Tmdb\Model\TmdbCollection;
use App\Lib\Tmdb\TmdbClient;
use Spark\Http\Response;
use Spark\Support\Str;
use Spark\Utils\Paginator;

/**
 * The Explore controller is a controller that handles all the exploration related routes.
 *
 * The class contains methods for exploring content by genre, original language, country, company, network, and people.
 * Each method takes a type of content and filter parameters and returns a response with the rendered view.
 *
 * @package Controllers
 */
class ExploreController
{
    /**
     * Explore content by genre.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param string $slug The slug containing the genre name and ID.
     * @return Response The response object containing the rendered view.
     */
    public function genre($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_genres' => substr($slug, strrpos($slug, '-') + 1)],
            Str::headline(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    /**
     * Explore content by original language.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param string $language The slug containing the language name and code.
     * @return Response The response object containing the rendered view.
     */
    public function language($type, $language)
    {
        $code = substr($language, strrpos($language, '-') + 1);

        return $this->explore(
            $type,
            ['with_original_language' => strtolower($code) != 'en' ? $code : null],
            Str::headline(
                substr($language, 0, strrpos($language, '-'))
            )
        );
    }

    /**
     * Explore content by country of origin.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param string $country The slug containing the country name and ISO 3166-1 code.
     * @return Response The response object containing the rendered view.
     */
    public function country($type, $country)
    {
        $code = substr($country, strrpos($country, '-') + 1);

        return $this->explore(
            $type,
            ['with_origin_country' => strtolower($code) != 'us' ? $code : null],
            Str::headline(
                substr($country, 0, strrpos($country, '-'))
            )
        );
    }

    /**
     * Explore content by production company.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param string $slug The slug containing the company name and ID.
     * @return Response The response object containing the rendered view.
     */
    public function company($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_companies' => substr($slug, strrpos($slug, '-') + 1)],
            Str::headline(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    /**
     * Explore content by network.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param string $slug The slug containing the network name and ID.
     * @return Response The response object containing the rendered view.
     */
    public function network($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_networks' => substr($slug, strrpos($slug, '-') + 1)],
            Str::headline(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    /**
     * Explore content based on specified filters.
     *
     * This method uses the TMDB client to retrieve filtered content data based on the provided type and filters.
     * It constructs a view model to organize the content and sets up pagination for the results. The rendered
     * content is then returned as a response from a specific template.
     *
     * @param string $type The type of content to explore (e.g., "movie", "tv").
     * @param array $filter An array of filters to apply when retrieving content.
     * @param string $title The title to display in the rendered view.
     * @return Response The response object containing the rendered view with the explored content.
     */
    protected function explore($type, $filter, $title): Response
    {
        // Create a new TMDB client instance for the specified type of content.
        $client = TmdbClient::create($type);

        // Retrieve the content data for the specified type with the given filters.
        // The page parameter is set to the current page number in the query string.
        $result = $client->find(array_merge($filter, ['page' => request()->query('page', 1)]));

        // Cache the genre data for the specified type.
        $cache = cache('tmdb.' . $type);
        $viewModel = new TmdbCollection(
            // Create a GenreCollection with the cached genre data.
            new GenreCollection(
                $cache->load('genres', fn() => $client->getGenre())
            ),
            // Set the content data to the result of the TMDB client request.
            [
                'content' => $result['results']
            ]
        );

        // Create a new Paginator instance with the total number of results and the page size set to 20.
        // If the total number of results is greater than or equal to 10000, set the total number of results to 10000.
        $pagination = new Paginator(($result['total_results'] >= 10000 ? 10000 : $result['total_results']), 20);

        // Set the data of the Paginator to the content data of the view model.
        $pagination->setData($viewModel->cut('content'));

        // Return a response containing the rendered view with the explored content.
        // The title of the page is set to the title parameter.
        return fireline('explore', [
            'heading' => __('Explore:') . ' <u>' . $title . '</u> ' . __($type == 'movie' ? 'Movies' : 'TV Series'),
            'paginator' => $pagination
        ]);
    }

    /**
     * A page for a person containing their filmography.
     *
     * @param string $slug The slug containing the person's name and ID.
     * @return Response The response object containing the rendered view with the person's information and filmography.
     */
    public function people($slug)
    {
        // Retrieve the person's information with their combined credits.
        $cache = cache('tmdb.home');
        $client = TmdbClient::create('movie');
        $result = $client->request('person/' . substr($slug, strrpos($slug, '-') + 1), ['append_to_response' => 'combined_credits']);

        // Create a new TmdbCollection with the movie genre data cached by the home page.
        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('movie.genres', fn() => $client->getGenre())
            ),
            [
                'movie' => array_filter($result['combined_credits']['cast'] ?? [], fn($media) => $media['media_type'] == 'movie'),
            ]
        );

        // Switch the client to the TV type and add the TV genre data to the view model.
        $client->setType('tv');

        // Add the TV series credits to the view model.
        $viewModel->setGenreCollection(
            new GenreCollection(
                $cache->load('tv.genres', fn() => $client->getGenre())
            ),
        )->addToCollection('tv-series', array_filter($result['combined_credits']['cast'] ?? [], fn($media) => $media['media_type'] == 'tv'), );

        // Remove the combined credits from the result to prevent exposing redundant data.
        unset($result['combined_credits']);

        // Return a response containing the rendered view with the person's information and filmography.
        return fireline('people', ['info' => $result, 'collection' => $viewModel->toArray()]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Core\Tmdb\Model\GenreCollection;
use App\Core\Tmdb\Model\TmdbCollection;
use App\Core\Tmdb\TmdbClient;
use VulcanPhp\Core\Helpers\Str;
use VulcanPhp\SimpleDb\Includes\Paginator\Paginator;

class Explore
{
    public function genre($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_genres' => substr($slug, strrpos($slug, '-') + 1)],
            Str::read(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    public function language($type, $language)
    {
        $code = substr($language, strrpos($language, '-') + 1);

        return $this->explore(
            $type,
            ['with_original_language' => Str::lowercase($code) != 'en' ? $code : null],
            Str::read(
                substr($language, 0, strrpos($language, '-'))
            )
        );
    }

    public function country($type, $country)
    {
        $code = substr($country, strrpos($country, '-') + 1);

        return $this->explore(
            $type,
            ['with_origin_country' => Str::lowercase($code) != 'us' ? $code : null],
            Str::read(
                substr($country, 0, strrpos($country, '-'))
            )
        );
    }

    public function company($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_companies' => substr($slug, strrpos($slug, '-') + 1)],
            Str::read(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    public function network($type, $slug)
    {
        return $this->explore(
            $type,
            ['with_networks' => substr($slug, strrpos($slug, '-') + 1)],
            Str::read(
                substr($slug, 0, strrpos($slug, '-'))
            )
        );
    }

    protected function explore($type, $filter, $title)
    {
        $client     = TmdbClient::create($type);
        $result     = $client->find(array_merge($filter, ['page' => input('page')]));
        $cache      = cache('tmdb.' . $type);
        $viewModel  = new TmdbCollection(
            new GenreCollection(
                $cache->load('genres', fn () => $client->getGenre())
            ),
            [
                'content' => $result['results']
            ]
        );

        $pagination = Paginator::create(($result['total_results'] >= 10000 ? 10000 : $result['total_results']), 20)
            ->setData($viewModel->cut('content'));

        return view('explore', [
            'heading' => translate('Explore:') . ' <u>' . $title . '</u> ' . translate(($type == 'movie' ? 'Movies' : 'TV Series')),
            'paginator' => $pagination
        ]);
    }

    public function people($slug)
    {
        $cache  = cache('tmdb.home');
        $client = TmdbClient::create('movie');
        $result = $client->request('person/' . substr($slug, strrpos($slug, '-') + 1), ['append_to_response' => 'combined_credits']);;

        $viewModel = new TmdbCollection(
            new GenreCollection(
                $cache->load('movie.genres', fn () => $client->getGenre())
            ),
            [
                'movie' => array_filter($result['combined_credits']['cast'] ?? [], fn ($media) => $media['media_type'] == 'movie'),
            ]
        );

        $client->setType('tv');

        $viewModel->setGenreCollection(
            new GenreCollection(
                $cache->load('tv.genres', fn () => $client->getGenre())
            ),
        )->addToCollection('tv-series', array_filter($result['combined_credits']['cast'] ?? [], fn ($media) => $media['media_type'] == 'tv'),);

        unset($result['combined_credits']);

        return view('people', ['info' => $result, 'collection' => $viewModel->toArray()]);
    }
}

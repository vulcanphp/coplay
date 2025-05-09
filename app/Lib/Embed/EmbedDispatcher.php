<?php

namespace App\Lib\Embed;

use App\Lib\Embed\Embed;
use App\Lib\Tmdb\TmdbClient;
use App\Lib\Tmdb\Model\TmdbContent;
use App\Lib\Embed\Includes\EmbedCrawler;
use App\Lib\Embed\Includes\EmbedFallback;
use App\Lib\Embed\Interfaces\IEmbedController;
use App\Lib\Tmdb\Exceptions\TmdbClientException;
use App\Lib\Embed\Includes\EmbedConfiguratorFromTmdb;
use Spark\Http\Request;
use Spark\Http\Response;

/**
 * Class EmbedDispatcher
 *
 * This class is an implementation of the IEmbedController interface.
 * It provides a way to dispatch requests to the embed controller.
 *
 * @package App\Lib\Embed
 */
class EmbedDispatcher implements IEmbedController
{
    /**
     * @param string $type The type of content (e.g. "movie", "tv")
     * @param int $tmdb The TMDB ID of the content
     */
    public function __construct(protected string $type, protected int $tmdb)
    {
    }

    /**
     * Creates a new instance of the EmbedDispatcher class.
     *
     * The returned instance is configured to dispatch requests for the specified
     * type and TMDB ID.
     *
     * @param string $type The type of content (e.g. "movie", "tv")
     * @param int $tmdb The TMDB ID of the content
     * @return EmbedDispatcher The newly created instance
     */
    public static function create(string $type, int $tmdb): EmbedDispatcher
    {
        return new EmbedDispatcher($type, $tmdb);
    }

    /**
     * Dispatches a request and returns a response with embedded video content.
     *
     * This method utilizes the TMDB API to retrieve video content information
     * and configures an Embed object for video embedding. If the requested video
     * content is unavailable from TMDB, a fallback mechanism is triggered.
     *
     * The method handles both TV series and movies, fetching necessary details
     * such as episodes for TV series and links for video playback. It also
     * incorporates an auto-embed feature if enabled.
     *
     * @param \Spark\Http\Request $request The incoming HTTP request.
     * @return \Spark\Http\Response The HTTP response containing the embedded video content or a fallback.
     */
    public function dispatch(Request $request): Response
    {
        /**
         * @var TmdbClient $client
         */
        $client = TmdbClient::create($this->type);

        /**
         * @var EmbedFallback $fallback
         */
        $fallback = new EmbedFallback();

        try {
            /**
             * @var TmdbContent $video
             */
            $video = new TmdbContent(
                $client->getInfo($this->tmdb, ['append_to_response' => 'external_ids,alternative_titles'])
            );
        } catch (TmdbClientException) {
            // if the video is not found in tmdb, trigger the fallback
            return $fallback->trigger();
        }

        /**
         * @var Embed $embed
         */
        $embed = new Embed(
            // use the tmdb video data to configure the embed
            new EmbedConfiguratorFromTmdb($video)
        );

        // set the callback url for the embed
        $embed->setCallbackUrl($request->getPath());

        // set the fallback configurator
        $fallback->setConfigurator($embed->getConfig());

        // get the play token from the request query
        $token = trim($request->query('play', ''));

        // if the token is not empty, resolve the embed with the crawler
        if (!empty($token)) {
            /**
             * @var EmbedCrawler $callback
             */
            $callback = new EmbedCrawler($embed->getConfig(), $fallback);

            // resolve the embed
            return $callback->resolve($token);
        }

        // Show auto embed links from tv series
        // if the content type is tv
        if ($this->type === 'tv') {

            // set the current episode
            $video->current_episode = $video->last_episode_to_air['episode_number'] ?? ($video->next_episode_to_air['episode_number'] ?? null);

            // get season number from client or set latest
            $video->seasons = collect($video->seasons)
                ->filter(fn($se) => ($se['season_number'] ?? 0) > 0 && isset($se['air_date']) && strtotime($se['air_date']) <= time())
                ->map(function ($se) {
                    return [
                        'name' => e($se['name']),
                        'air_date' => $se['air_date'],
                        'season_number' => $se['season_number'],
                        'poster_path' => $se['poster_path'],
                    ];
                })
                ->all();

            if (!empty($video->seasons)) {
                // set the latest season
                $video->number_of_seasons = max(array_column($video->seasons, 'season_number'));
                $video->season = $request->query('season', $video->number_of_seasons);
                $video->seasons = array_reverse($video->seasons);

                try {
                    // fetch episodes form season
                    $video->episodes = collect($client->getEpisodes($this->tmdb, $video->season));
                } catch (TmdbClientException $e) {

                    // if the video is not found in tmdb, trigger the fallback
                    return $fallback->trigger();
                }

                $video->episodes = $video->episodes->filter(
                    fn($ep) => ($ep['season_number'] ?? 0) > 0 && ($ep['episode_number'] ?? 0) > 0 && (!isset($ep['air_date']) || (isset($ep['air_date']) && strtotime($ep['air_date']) <= time()))
                )
                    ->filter(
                        fn($ep) => !isset($video->current_episode) || (isset($video->current_episode) && $ep['episode_number'] <= $video->current_episode)
                    )
                    ->map(function ($episode) use ($embed) {

                        $links = [];

                        // check if auto embed is enabled
                        // if it is, add the season and episode number to the embed
                        // and get the links
                        if (is_feature_enabled('auto_embed')) {
                            $embed->getConfig()
                                ->push('season', $episode['season_number'])
                                ->push('episode', $episode['episode_number']);

                            $links = array_merge($links, $embed->getLinks());
                        }

                        if (empty($links)) {
                            return null;
                        }

                        return [
                            'air_date' => $episode['air_date'],
                            'episode_number' => $episode['episode_number'],
                            'season_number' => $episode['season_number'],
                            'name' => e($episode['name']),
                            'still_path' => $episode['still_path'],
                            'links' => $links,
                        ];
                    })
                    ->filter(fn($ep) => !empty($ep))
                    ->all();

                if (!empty($video->episodes)) {
                    $video->episodes = array_reverse($video->episodes);

                    // get episode number from client or set latest
                    $video->episode = $request->query('episode', $video->episodes[0]['episode_number']);
                    $episode = collect($video->episodes)
                        ->filter(
                            fn($ep) => $ep['episode_number'] == intval($video->episode)
                        )
                        ->first();
                    $video->links = !empty($episode) ? $episode['links'] : [];
                }
            }
        } else {

            $links = [];

            // get the links for the embed player
            // if the auto embed feature is enabled
            if (is_feature_enabled('auto_embed')) {
                $links = array_merge($links, $embed->getLinks());
            }

            $video->links = $links;
        }

        // if the video has links return the embed
        if (isset($video->links) && !empty($video->links)) {
            return view('embed/index', ['video' => $video]);
        }

        // if the video is does not have any links, trigger the fallback
        return $fallback->trigger();
    }

    /**
     * Checks if the request origin is allowed.
     *
     * @param \Spark\Http\Request $request
     * @return self
     */
    public function checkOrigin(Request $request): self
    {
        // Check if the API feature is enabled, otherwise, allow all requests
        if (!is_feature_enabled('api')) {
            return $this;
        }

        // Check if the request origin is allowed
        if ($request->header('http-sec-fetch-site') === 'cross-site') {
            // If the request origin is not allowed, return a 404 page
            abort(404);
        }

        return $this;
    }
}

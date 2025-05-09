<?php

namespace App\Lib\Embed\Includes;

use App\Lib\Embed\Exceptions\EmbedCrawlerException;
use App\Lib\Embed\Interfaces\IEmbedCrawler;
use Spark\Http\Response;

/**
 * The EmbedCrawler class implements the IEmbedCrawler interface and provides
 * an implementation of the EmbedCrawler.
 *
 * The EmbedCrawler is responsible for resolving a given token to a playable
 * link. It uses the application's hash service to decrypt the token and then
 * calls the callback function associated with the decrypted token's key.
 * 
 * @package App\Lib\Embed\Includes
 * @version 1.0.0
 */
class EmbedCrawler implements IEmbedCrawler
{
    /**
     * The array of crawlers.
     *
     * The crawlers are registered under a key that is used to identify the
     * crawler. The crawler is a callable that takes a string argument and
     * returns a Response object.
     *
     * @var array<string, callable>
     */
    protected array $crawlers = [];

    /**
     * Constructor for the EmbedCrawler class.
     *
     * @param EmbedConfigurator $config The configuration object.
     * @param EmbedFallback $fallback The fallback object.
     */
    public function __construct(protected EmbedConfigurator $config, protected EmbedFallback $fallback)
    {
        $this->setDefaultCrawlers();
    }

    /**
     * Registers the default crawlers.
     *
     * This method is called from the constructor and sets up the default crawlers.
     * It can be overridden in a subclass to add or change the default crawlers.
     */
    public function setDefaultCrawlers(): void
    {
        // register the default crawlers
    }

    /**
     * Checks if a crawler is registered for the given ID.
     *
     * @param string $id The unique identifier for the crawler to check.
     * @return bool True if a crawler is registered, false otherwise.
     */
    public function hasCrawler(string $id): bool
    {
        return isset($this->crawlers[$id]) && is_callable($this->crawlers[$id]);
    }

    /**
     * Registers a crawler with a specified ID and callback function.
     *
     * @param string $id The unique identifier for the crawler.
     * @param callable $callback The callback function to be executed for the crawler.
     */
    public function setCrawler(string $id, $callback): void
    {
        $this->crawlers[$id] = $callback;
    }

    /**
     * Gets the fallback instance to be used if no crawler can be found.
     *
     * @return EmbedFallback The fallback instance
     */
    public function getFallback(): EmbedFallback
    {
        return $this->fallback;
    }

    /**
     * Resolve a given token to a playable link.
     *
     * @param  string  $token
     * @return \Spark\Http\Response
     */
    public function resolve(string $token): Response
    {
        // decrypt the token
        $token = json_decode(
            // use the application's hash service to decrypt the token
            hashing()->decrypt($token),
            true
        );

        // if the token is not a valid array
        if (!is_array($token) && config('debug')) {
            // throw an exception
            throw new EmbedCrawlerException('Invalid Playback Token');
        }

        // resolve the callback with the given token type
        return match ($token['type']) {
            'crawler' => $this->resolveCrawler($token),
            default => $this->resolveCallback($token),
        };
    }

    /**
     * Resolves the given token to a playable link by calling the associated crawler.
     *
     * @param  array  $token The token to resolve.
     * @return \Spark\Http\Response The response from the crawler.
     */
    private function resolveCrawler(array $token): Response
    {
        // if the token does not have a matching crawler
        if (!$this->hasCrawler($token['id']) && config('debug')) {
            // throw an exception
            throw new EmbedCrawlerException('Callback does not exists for (' . $token['id'] . ')');
        }


        // call the callback function with the token's segment as the argument
        if ($this->hasCrawler($token['id'])) {
            call_user_func($this->crawlers[$token['id']], $token['segment']);
        }

        // return the response of the fallback
        return $this->getFallback()->trigger();
    }

    /**
     * Resolves the given token to a playable link by calling the associated callback function.
     *
     * @param  array  $token The token to resolve.
     * @return Response The response from the callback function.
     */
    private function resolveCallback(array $token): Response
    {
        // get the embed configuration for the given ID
        $embedConfig = $this->config->getEmbedConfig($token['id']);

        // if the embed configuration does not contain a callback for the given type
        if (!isset($embedConfig[$token['type']])) {
            // return the response of the fallback
            return $this->getFallback()->trigger();
        }

        // decode the callback code from HTML entities
        $code = html_entity_decode($embedConfig[$token['type']]);

        // extract the config and embed source variables
        extract(['config' => $this->config, 'embedSource' => $token['segment']]);

        // execute the callback code
        eval ($code);

        // return the response of the fallback
        return $this->getFallback()->trigger();
    }
}

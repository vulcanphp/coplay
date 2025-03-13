<?php

namespace Lib\Embed;

use Hyper\Utils\Hash;
use Lib\Embed\Includes\AutoUpdate;
use Lib\Embed\Includes\EmbedConfigurator;
use Lib\Embed\Exceptions\EmbedException;
use Lib\Embed\Interfaces\IEmbed;

/**
 * This is a Class which Embed Movies, TV Series, Anime, Drama from various websites or server
 * This Application does not store any content only serve from third party sources by tmdb or imdb id
 * This Class is Fully Functional and Automatically Update New Sources to Embed Content from third party sources
 * 
 * @author Shahin Moyshan
 * @link https://github.com/vulcanphp/coplay
 * @version 1.0.0
 */
class Embed implements IEmbed
{
    /**
     * The URL to redirect to after the embed is loaded.
     * @var string
     */
    protected $callbackUrl;

    /**
     * @var AutoUpdate The auto update object.
     */
    protected $updater;

    /**
     * @param EmbedConfigurator $config
     */
    public function __construct(protected EmbedConfigurator $config)
    {
        /**
         * @var AutoUpdate
         * Create a new instance of AutoUpdate with the mode set to 'monthly'.
         */
        $this->updater = new AutoUpdate('monthly');
    }

    /**
     * Retrieves the embed configuration.
     *
     * @return EmbedConfigurator The configuration object for the embed.
     */
    public function getConfig(): EmbedConfigurator
    {
        return $this->config;
    }

    /**
     * Gets the auto update object.
     *
     * @return AutoUpdate The auto update object.
     */
    public function getUpdater(): AutoUpdate
    {
        return $this->updater;
    }

    /**
     * Sets the callback URL.
     *
     * @param string $url The callback URL to set.
     * @return self Returns the current instance for method chaining.
     */
    public function setCallbackUrl(string $url): self
    {
        $this->callbackUrl = $url;
        return $this;
    }

    /**
     * Get the links for the embed.
     *
     * @return array The embed links.
     * @throws EmbedException If the type is invalid.
     */
    public function getLinks(): array
    {
        // Check if the content type is valid
        if (!in_array($this->config->get('type'), ['movie', 'tv', 'anime', 'drama'])) {
            // Throw an exception if the type is invalid
            throw new EmbedException('Invalid Type: ' . $this->config->get('type'));
        }

        // If the auto embed update feature is enabled, perform the update check
        if (is_feature_enabled('auto_embed_update')) {
            // Check and update the schema if necessary
            $this->getUpdater()->check();
        }

        $embeds = [];
        foreach ($this->config->getSchema() as $id => $embed) {
            // Check if the embed has a when condition and if it does, check if the condition is met
            if (isset($embed['when']) && !empty($embed['when']) && $this->embedCondition($embed['when']) === false) {
                continue;
            }

            // Get the embed frames for the current embed
            foreach ($this->getEmbedFrame($id, $embed['source']) as $index => $source) {
                // Set the source of the embed to the current frame
                $embed['source'] = $source;
                // Set the id of the embed to the current id with a suffix of the index
                $embed['id'] .= $index > 0 ? '-' . $index + 1 : '';
                // Set the name of the embed to the current name with a suffix of the index
                $embed['name'] .= $index > 0 ? ' (' . $index + 1 . ')' : '';
                // Add the embed to the collection
                $embeds[] = $embed;
            }
        }

        // Sort the embeds by priority in descending order
        return collect($embeds)
            ->multiSort('priority', true)
            ->all();
    }

    /**
     * Gets the embed frames for the given id and source.
     *
     * @param string $id The id of the embed.
     * @param mixed $source The source of the embed.
     * @return array The embed frames.
     */
    protected function getEmbedFrame(string $id, $source): array
    {
        // Get the segment of the source by the type
        $segment = (is_array($source)) ? ($source[$this->config->get('type')]
            ?? (in_array($this->config->get('type'), ['drama', 'anime'])
                ? ($source['tv'] ?? null) : null
            )) : $source;

        $frames = [];

        // If the segment is not empty, loop through the segment and parse the url
        if (!empty($segment)) {
            foreach ((array) $segment as $frame) {
                // Prepare the frame by adding the base url of the source
                $frame = ($source['url'] ?? '') . $frame;

                // Check if the frame contains "crawler or callback" and if it does, get the callback url
                if (preg_match('/\{([a-zA-Z_][a-zA-Z0-9_]*)\(\)\}/', $frame, $matches)) {
                    $frames[] = $this->getCallbackUrl($id, $frame, $matches[1]);
                } else {
                    // Parse the URL by adding the base URL of the source
                    $frames[] = $this->config->replaceEmbedUrlSegments($frame);
                }
            }
        }

        return $frames;
    }

    /**
     * Generates the callback URL with the encrypted parameters.
     *
     * @param string $id The id of the embed.
     * @param string $segment The segment of the source.
     * @param string $type The type of the embed.
     * @return string The callback URL with the encrypted parameters.
     */
    protected function getCallbackUrl(string $id, string $segment, string $type): string
    {
        if (!isset($this->callbackUrl)) {
            throw new EmbedException('Callback Url does not specified');
        }

        // Encrypt the parameters with the hash class
        $encryptedParams = get(Hash::class)->encrypt(
            json_encode(['id' => $id, 'type' => $type, 'segment' => $segment])
        );

        // Return the callback URL with the encrypted parameters
        return "{$this->callbackUrl}?play=$encryptedParams";
    }

    /**
     * Checks if the embed is valid according to the given condition.
     *
     * The condition is an array that contains the rules to check the embed.
     * The rules are:
     * - type: An array of strings that represent the allowed types of the embed.
     * - genres: An array of integers that represent the allowed genres of the embed.
     * - countries: An array of strings that represent the allowed countries of the embed.
     * - !type: An array of strings that represent the not allowed types of the embed.
     * - !genres: An array of integers that represent the not allowed genres of the embed.
     * - !countries: An array of strings that represent the not allowed countries of the embed.
     *
     * @param array $condition The condition to check the embed.
     * @return bool True if the embed is valid according to the condition, false otherwise.
     */
    protected function embedCondition(array $condition): bool
    {
        // A helper function to check if an array has any value from another array
        $hasAnyArrayValue = function ($array1, $array2): bool {
            foreach ($array2 as $value) {
                if (in_array($value, $array1)) {
                    return true;
                }
            }

            return false;
        };

        // Check if the embed type is allowed
        $typeAllowed = !isset($condition['type']) || (isset($condition['type']) && in_array($this->config->get('type'), (array) $condition['type']));
        // Check if the embed genres are allowed
        $genresAllowed = !isset($condition['genres']) || (isset($condition['genres']) && $hasAnyArrayValue(array_column($this->config->get('genres'), 'id'), (array) $condition['genres']));
        // Check if the embed countries are allowed
        $countriesAllowed = !isset($condition['countries']) || (isset($condition['countries']) && $hasAnyArrayValue($this->config->get('countries'), (array) $condition['countries']));

        // Check if the embed type is not allowed
        $typeNotAllowed = !isset($condition['!type']) || (isset($condition['!type']) && !in_array($this->config->get('type'), (array) $condition['!type']));
        // Check if the embed genres are not allowed
        $genresNotAllowed = !isset($condition['!genres']) || (isset($condition['!genres']) && !$hasAnyArrayValue(array_column($this->config->get('genres'), 'id'), (array) $condition['!genres']));
        // Check if the embed countries are not allowed
        $countriesNotAllowed = !isset($condition['!countries']) || (isset($condition['!countries']) && !$hasAnyArrayValue($this->config->get('countries'), (array) $condition['!countries']));

        // Return true if the embed is valid according to the condition
        return $typeAllowed && $genresAllowed && $countriesAllowed && $typeNotAllowed && $genresNotAllowed && $countriesNotAllowed;
    }
}

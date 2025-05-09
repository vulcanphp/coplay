<?php

namespace App\Lib\Embed\Includes;

use App\Lib\Embed\Interfaces\IEmbedConfigurator;

/**
 * Class EmbedConfigurator
 *
 * This class is an implementation of the IEmbedConfigurator interface.
 * It provides methods to get, set, and parse the configuration array for the embed.
 *
 * @package App\Lib\Embed\Includes
 */
class EmbedConfigurator implements IEmbedConfigurator
{
    /**
     * @var array Configuration array for the embed.
     */
    protected array $config;

    /**
     * @var array Segment array for placeholders.
     */
    protected array $segment;

    /**
     * @var array Schema array for the embed structure.
     */
    protected array $schema;

    /**
     * Constructor for the EmbedConfigurator class.
     *
     * @param array $config Initial configuration array.
     */
    public function __construct(array $config)
    {
        $this->set($config);
    }

    /**
     * Sets the configuration array for the embed.
     *
     * @param array $config Initial configuration array.
     *
     * @return self
     */
    public function set(array $config): self
    {
        // Set the configuration array
        $this->config = $config;

        // Set the segment array for placeholders
        $this->segment = [
            '{tmdb}' => $this->get('tmdb'), // TMDB ID
            '{imdb}' => $this->get('imdb'), // IMDB ID
            '{type}' => $this->get('type'), // Type of the content (movie/tv)
            '{id}' => $this->get('id'), // ID of the content
            '{key}' => $this->get('key'), // Key/Name of the content
            '{query}' => $this->get('query'), // Query to search for the content
            '{season}' => $this->get('season'), // Season number
            '{episode}' => $this->get('episode'), // Episode number
            '{title}' => $this->get('title'), // Title of the content
            '{slug}' => $this->get('slug'), // Slug of the content
            '{original_title}' => $this->get('original_title'), // Original title of the content
            '{original_slug}' => $this->get('original_slug'), // Original slug of the content
            '{year}' => $this->get('year'), // Year of release
            '{date}' => $this->get('date'), // Release date in Y-m-d format
            '{zero|season}' => sprintf('%02d', $this->get('season')), // Season number with leading zeros
            '{zero|episode}' => sprintf('%02d', $this->get('episode')), // Episode number with leading zeros
            '{-if|season}' => $this->get('season') > 1 ? sprintf('-season-%d', $this->get('season')) : '', // Conditional for the season number
            '{-if|episode}' => $this->get('episode') > 1 ? sprintf('-episode-%d', $this->get('episode')) : '', // Conditional for the episode number
            '{crawler()}' => $this->get('crawler'), // Crawler object
        ];

        return $this;
    }

    /**
     * Adds a value to the configuration.
     *
     * @param string $key The key of the value to add.
     * @param mixed $value The value to add.
     *
     * @return self
     */
    public function push(string $key, $value): self
    {
        $this->config[$key] = $value;
        return $this->set($this->config); // Update the configuration segments
    }

    /**
     * Returns a value from the configuration array.
     *
     * @param string|null $key The key of the value to retrieve.
     * @param mixed $default The default value to return if the key is not found.
     *
     * @return mixed The value from the configuration array or the default value.
     */
    public function get(?string $key = null, $default = null)
    {
        return $key !== null ? ($this->config[$key] ?? $default) : $this->config;
    }

    /**
     * Returns the schema array for the embed structure.
     * 
     * This method uses a cached schema array and only reads the schema file
     * if the cache is empty.
     * 
     * @return array The schema array for the embed structure.
     */
    public function getSchema(): array
    {
        return $this->schema ??= collect(
            (array) json_decode(
                file_get_contents(storage_dir('temp/embed_schema.json')),
                true
            )
        )
            ->filter(
                fn($embed) => (
                    $embed['disabled'] ?? false) !== true
                && isset($embed['id']) && isset($embed['source']) && !empty($embed['source']) && isset($embed['name'])
            )
            ->all();
    }

    /**
     * Retrieves the embed configuration for a given ID.
     *
     * @param string $id The identifier for the embed configuration.
     * @return array The embed configuration array for the specified ID, or an empty array if not found.
     */
    public function getEmbedConfig(string $id): array
    {
        return $this->getSchema()[$id] ?? [];
    }

    /**
     * Converts the given URL by replacing placeholders with their corresponding values.
     *
     * @param string $url The URL containing placeholders to be replaced.
     * @return string The URL with placeholders replaced by actual values from the segment array.
     */
    public function replaceEmbedUrlSegments(string $url): string
    {
        $embedUrl = str_ireplace(array_keys($this->segment), $this->segment, $url);

        // Remove callback placeholders from the URL
        return preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\(\)\}/', '', $embedUrl);
    }
}

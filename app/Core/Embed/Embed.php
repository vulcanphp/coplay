<?php

namespace App\Core\Embed;

use App\Core\Embed\Includes\AutoUpdate;
use App\Core\Embed\Includes\EmbedConfigurator;
use App\Core\Embed\Exceptions\EmbedException;
use App\Core\Embed\Interfaces\IEmbed;
use VulcanPhp\Core\Crypto\Encryption;
use VulcanPhp\Core\Helpers\Arr;

/**
 * This is a Class which Embed Movies, TV Series, Anime, Drama from various websites or server
 * This Application does not store any content only serve from third party sources by tmdb or imdb id
 * This Class is Fully Functional and Automatically Update New Sources to Embed Content from third party sources
 * 
 * @version 1.0.0
 * @author Shahin Moyshan
 * @see https://github.com/vulcanphp
 * @package FreePlay
 * @link https://github.com/vulcanphp/freeplay
 */
class Embed implements IEmbed
{
    protected $callbackUrl, $updater;

    public function __construct(protected EmbedConfigurator $config)
    {
        $this->updater = new AutoUpdate('monthly');
    }

    public function getConfig(): EmbedConfigurator
    {
        return $this->config;
    }

    public function getUpdater(): AutoUpdate
    {
        return $this->updater;
    }

    public function setCallbackUrl(string $url): self
    {
        $this->callbackUrl = $url;
        return $this;
    }

    public function getLinks(): array
    {
        if (!in_array($this->config->get('type'), ['movie', 'tv', 'anime', 'drama'])) {
            throw new EmbedException('Invalid Type: ' . $this->config->get('type'));
        }

        $this->getUpdater()->check();

        $embeds  = [];
        foreach ($this->config->getSchema() as $id => $embed) {

            if (isset($embed['when']) && !empty($embed['when']) && $this->embedCondition($embed['when']) === false) {
                continue;
            }

            foreach ($this->getEmbedFrame($id, $embed['source']) as $index => $source) {
                $embed['source'] = $source;
                $embed['id'] = $embed['id'] . ($index > 0 ? '-' . $index + 1 : '');
                $embed['name'] = $embed['name'] . ($index > 0 ? ' (' . $index + 1 . ')' : '');
                $embeds[] = $embed;
            }
        }

        return Arr::multisort($embeds, 'priority', true);
    }

    protected function getEmbedFrame(string $id, $source)
    {
        $segment = (is_array($source)) ? ($source[$this->config->get('type')]
            ?? (in_array($this->config->get('type'), ['drama', 'anime'])
                ? ($source['tv'] ?? null) : null
            )) : $source;

        $frames = [];

        if (!empty($segment)) {
            foreach ((array) $segment as $frame) {
                if (stripos($frame, '{crawler()}') !== false) {
                    $frames[] = $this->getCallbackUrl($id, $frame);
                } else {
                    $frames[] = $this->config->parseUrl(($source['url'] ?? '') . $frame);
                }
            }
        }

        return $frames;
    }

    protected function getCallbackUrl(string $id, string $segment): string
    {
        if (!isset($this->callbackUrl)) {
            throw new EmbedException('Callback Url does not specified');
        }

        return $this->callbackUrl . '?play=' . Encryption::encryptArray(['id' => $id, 'segment' => $segment]);
    }

    protected function embedCondition(array $condition): bool
    {
        return ((!isset($condition['type']) || (isset($condition['type']) && in_array($this->config->get('type'), (array) $condition['type'])))
            && (!isset($condition['genres']) || (isset($condition['genres']) && Arr::hasAnyValues(array_column($this->config->get('genres'), 'id'), (array) $condition['genres'])))
            && (!isset($condition['countries']) || (isset($condition['countries']) && Arr::hasAnyValues($this->config->get('countries'), (array) $condition['countries']))))
            && (
                (!isset($condition['!type']) || (isset($condition['!type']) && !in_array($this->config->get('type'), (array) $condition['!type'])))
                && (!isset($condition['!genres']) || (isset($condition['!genres']) && !Arr::hasAnyValues(array_column($this->config->get('genres'), 'id'), (array) $condition['!genres'])))
                && (!isset($condition['!countries']) || (isset($condition['!countries']) && !Arr::hasAnyValues($this->config->get('countries'), (array) $condition['!countries'])))
            );
    }
}

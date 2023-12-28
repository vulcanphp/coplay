<?php

namespace App\Core\Embed\Includes;

use App\Core\Embed\Interfaces\IEmbedConfigurator;

class EmbedConfigurator implements IEmbedConfigurator
{
    protected array $config, $segment, $schema;

    public function __construct(array $config)
    {
        $this->set($config);
    }

    public function set(array $config): self
    {
        $this->config   = $config;
        $this->segment  = [
            '{tmdb}'            => $this->get('tmdb'),
            '{imdb}'            => $this->get('imdb'),
            '{type}'            => $this->get('type'),
            '{id}'              => $this->get('id'),
            '{key}'             => $this->get('key'),
            '{query}'           => $this->get('query'),
            '{season}'          => $this->get('season'),
            '{episode}'         => $this->get('episode'),
            '{title}'           => $this->get('title'),
            '{slug}'            => $this->get('slug'),
            '{original_title}'  => $this->get('original_title'),
            '{original_slug}'   => $this->get('original_slug'),
            '{year}'            => $this->get('year'),
            '{date}'            => $this->get('date'),
            '{zero|season}'     => sprintf('%02d', $this->get('season')),
            '{zero|episode}'    => sprintf('%02d', $this->get('episode')),
            '{-if|season}'      => $this->get('season') > 1 ? sprintf('-season-%d', $this->get('season')) : '',
            '{-if|episode}'     => $this->get('episode') > 1 ? sprintf('-episode-%d', $this->get('episode')) : '',
            '{crawler()}'       => $this->get('crawler'),
        ];

        return $this;
    }

    public function push(string $key, $value): self
    {
        $this->config[$key] = $value;
        return $this->set($this->config);
    }

    public function get(?string $key = null, $default = null)
    {
        return $key !== null ? ($this->config[$key] ?? $default) : $this->config;
    }

    public function getSchema(): array
    {
        return $this->schema ??= json_decode(file_get_contents(dirname(__DIR__) . '/schema.json'), true);
    }

    public function parseUrl(string $url): string
    {
        return str_ireplace(array_keys($this->segment), $this->segment, $url);
    }
}

<?php

namespace App\Core\Embed\Includes;

use App\Core\Embed\Crawler\VidCloud;
use App\Core\Embed\Exceptions\EmbedCrawlerException;
use App\Core\Embed\Interfaces\IEmbedCrawler;
use VulcanPhp\Core\Crypto\Encryption;

class EmbedCrawler implements IEmbedCrawler
{
    protected array $crawlers = [];

    public function __construct(protected EmbedConfigurator $config, protected EmbedFallback $fallback)
    {
        $this->setDefaultCrawlers();
    }

    public function setDefaultCrawlers(): void
    {
        // set VidCloud crawler
        $vidcloud = function (string $segment) {
            $endpoint   = parse_url($segment);
            $kvid       = new VidCloud($endpoint['scheme'] . '://' . $endpoint['host'], $this->config);
            $id         = $kvid->getId();

            if ($id !== null) {
                $this->config->push('crawler', $id);

                redirect($this->config->parseUrl($segment));
            }

            return false;
        };

        // register crawler for embed id
        $this->setCrawler('playtaku', $vidcloud);
        $this->setCrawler('dradplay', $vidcloud);
    }

    public function hasCrawler(string $id): bool
    {
        return isset($this->crawlers[$id]);
    }

    public function setCrawler(string $id, $callback): void
    {
        $this->crawlers[$id] = $callback;
    }

    public function getFallback(): EmbedFallback
    {
        return $this->fallback;
    }

    public function resolve(string $token): string
    {
        $token = Encryption::decryptArray($token);

        if (!is_array($token)) {
            throw new EmbedCrawlerException('Invalid Playback Token');
        }

        if (!$this->hasCrawler($token['id'])) {
            throw new EmbedCrawlerException('Callback does not exists for (' . $token['id'] . ')');
        }

        if (!call_user_func($this->crawlers[$token['id']], $token['segment'])) {
            return $this->getFallback()->trigger();
        }
    }
}

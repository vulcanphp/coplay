<?php

namespace VulcanPhp\FastCache\Engine\SimpleCache;

use VulcanPhp\FastCache\Interfaces\ICacheHandler;
use VulcanPhp\FastCache\Interfaces\IEngine;

class SimpleCache implements IEngine
{
    protected $cachepath, $extension;

    public function __construct(array $setup = [])
    {
        $this->setCachePath($setup['path'] ?? getcwd() . '/tmp')
            ->setExtension($setup['extension'] ?? '.cache');
    }

    public function setCachePath(string $path): self
    {
        $this->cachepath = $path;
        return $this;
    }

    public function getCachePath(): string
    {
        return $this->cachepath;
    }

    public function setExtension(string $ext): self
    {
        $this->extension = $ext;
        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function newCache(string $name): ICacheHandler
    {
        return new SimpleCacheHandler(
            $this->getCachePath()
                . DIRECTORY_SEPARATOR
                . sha1($name)
                . $this->getExtension()
        );
    }

    public function flush(): void
    {
        $ext = str_replace('.', '', $this->getExtension());
        foreach (scandir($this->getCachePath()) as $cache) {
            if (pathinfo($cache, PATHINFO_EXTENSION) != $ext) {
                continue;
            }

            unlink($this->getCachePath() . DIRECTORY_SEPARATOR .  $cache);
        }
    }
}

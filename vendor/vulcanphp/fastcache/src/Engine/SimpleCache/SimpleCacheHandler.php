<?php

namespace VulcanPhp\FastCache\Engine\SimpleCache;

use VulcanPhp\FastCache\Exceptions\SimpleCacheException;
use VulcanPhp\FastCache\Interfaces\ICacheHandler;

class SimpleCacheHandler implements ICacheHandler
{
    protected string $filepath;
    protected array $cacheData;
    protected bool $erased = false, $cached = false;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public function Reload(): self
    {
        if (!$this->cached) {
            $this->cached = true;
            if (!isset($this->cacheData) && file_exists($this->filepath)) {
                $this->cacheData = (array) json_decode(
                    file_get_contents($this->filepath),
                    true
                );
            } else {
                $this->cacheData = [];
            }
        }

        return $this;
    }

    public function getCacheFile(): string
    {
        return $this->filepath;
    }

    public function getCacheData(?string $key = null): ?array
    {
        $this->Reload();

        return $key !== null
            ? ($this->cacheData[$key] ?? null)
            : (array) $this->cacheData;
    }

    public function saveCacheFile(): self
    {
        $dir = dirname($this->filepath);
        if ((!is_dir($dir) && !mkdir($dir, 0777, true))
            || (!is_writable($dir) && !chmod($dir, 0777))
        ) {
            throw new SimpleCacheException('Failed to access Temp Directory..');
        }

        file_put_contents(
            $this->filepath,
            json_encode(
                $this->getCacheData(),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );

        return $this;
    }

    public function hasCache(string $key, bool $eraseExpired = false): bool
    {
        $this->Reload();

        if ($eraseExpired !== null) {
            $this->eraseExpired();
        }

        return isset($this->cacheData[$key]);
    }

    public function unsetCache(string $key): self
    {
        $this->Reload();
        unset($this->cacheData[$key]);

        return $this;
    }

    public function setCacheData(array $CacheData): self
    {
        $this->cacheData = $CacheData;
        return $this->saveCacheFile();
    }

    public function updateCacheData(string $key, array $data): self
    {
        $this->Reload();
        $this->cacheData[$key] = $data;

        return $this->saveCacheFile();
    }

    public function store(string $key, $data,  ?string $expire = null): self
    {
        return $this->updateCacheData($key, [
            'time'   => time(),
            'expire' => $expire !== null ? $this->getExpireTime($expire) : 0,
            'data'   => serialize($data),
        ]);
    }

    public function load(string $key, callable $callback,  ?string $expire = null)
    {
        if ($expire !== null) {
            $this->eraseExpired();
        }

        if (!$this->hasCache($key)) {
            $this->store(
                $key,
                call_user_func($callback),
                $expire
            );
        }

        return $this->retrieve($key);
    }

    public function retrieve($keys, bool $eraseExpired = false)
    {
        if ($eraseExpired) {
            $this->eraseExpired();
        }

        $results = [];

        foreach ((array) $keys as $key) {
            if ($this->hasCache($key)) {
                $results[$key] = unserialize($this->getCacheData($key)['data']);
            }
        }

        return is_array($keys) ? $results : ($results[$keys] ?? null);
    }

    public function retrieveAll(bool $eraseExpired = false): array
    {
        if ($eraseExpired) {
            $this->eraseExpired();
        }

        $results = [];
        foreach ((array) $this->getCacheData() as $key => $value) {
            $results[$key] = unserialize($value['data']);
        }

        return $results;
    }

    public function erase($keys): self
    {
        foreach ((array) $keys as $key) {
            $this->unsetCache($key);
        }

        return $this->saveCacheFile();
    }

    public function eraseExpired(): self
    {
        if (!$this->erased) {
            $this->erased = true;
            $count = 0;

            foreach ($this->getCacheData() as $key => $entry) {
                if ($this->isExpired($entry['time'], $entry['expire'])) {
                    $this->unsetCache($key);
                    $count++;
                }
            }

            if ($count > 0) {
                $this->saveCacheFile();
            }
        }

        return $this;
    }

    public function flush(): self
    {
        if (file_exists($this->filepath)) {
            unlink($this->filepath);
            $this->close();
        }

        return $this;
    }

    public function close(): self
    {
        unset($this->cacheData);

        $this->erased = false;
        $this->cached = false;

        return $this;
    }

    protected function getExpireTime(string $duration): int
    {
        return strtotime($duration) - time();
    }

    protected function isExpired(int $timestamp, int $expiration): bool
    {
        return $expiration !== 0 && ((time() - $timestamp) > $expiration);
    }
}

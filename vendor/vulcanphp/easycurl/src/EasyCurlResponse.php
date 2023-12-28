<?php

namespace VulcanPhp\EasyCurl;

use VulcanPhp\EasyCurl\Exceptions\EasyCurlException;
use VulcanPhp\EasyCurl\Interfaces\ICurlResponse;

class EasyCurlResponse implements ICurlResponse
{
    protected array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getResponse(?string $key = null): mixed
    {
        return $key !== null ? ($this->response[$key] ?? null) : $this->response;
    }

    public function getStatus(): int
    {
        return $this->getResponse('status');
    }

    public function getBody(): string
    {
        return $this->getResponse('body');
    }

    public function getJson(): array
    {
        return (array) json_decode($this->body(), true, JSON_UNESCAPED_UNICODE);
    }

    public function getLength(): int
    {
        return $this->getResponse('length');;
    }

    public function getLastUrl(): string
    {
        return $this->getResponse('last_url');;
    }

    public function __call($name, $arguments)
    {
        $method = 'get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], ...$arguments);
        }

        throw new EasyCurlException('Undefined Method: ' . $name);
    }
}

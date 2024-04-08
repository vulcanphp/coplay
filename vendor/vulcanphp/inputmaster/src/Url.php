<?php

namespace VulcanPhp\InputMaster;

use JsonSerializable;
use VulcanPhp\InputMaster\Exceptions\MalformedUrlException;

class Url implements JsonSerializable
{
    protected $originalUrl, $scheme, $host, $port, $path, $fragment, $username, $password, $params = [];

    public function __construct(?string $url = null)
    {
        $this->originalUrl = $url;

        if ($url !== null) {
            $data = $this->parseUrl($url);
            $this->setScheme($data['scheme'] ?? null)
                ->setHost($data['host'] ?? null)
                ->setPort($data['port'] ?? null)
                ->setPath($data['path'] ?? null)
                ->setFragment($data['fragment'] ?? null)
                ->setUsername($data['user'] ?? null)
                ->setPassword($data['pass'] ?? null)
                ->setQueryString($data['query'] ?? null);
        }
    }

    protected function parseUrl(string $url): array
    {
        $parts = parse_url(
            preg_replace_callback(
                '/[^:\/@?&=#]+/u',
                static function ($matches) {
                    return urlencode($matches[0]);
                },
                $url
            )
        );
        if ($parts === false) {
            throw new MalformedUrlException(
                sprintf('Failed to parse url: "%s"', $url)
            );
        }
        return array_map('urldecode', $parts);
    }

    public function isSecure(): bool
    {
        return (strtolower($this->getScheme() ?? '') === 'https');
    }

    public function isRelative(): bool
    {
        return ($this->getHost() === null);
    }

    public function getScheme(): ?string
    {
        return $this->scheme;
    }

    public function setScheme(?string $scheme): self
    {
        $this->scheme = $scheme;
        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(?string $host): self
    {
        $this->host = $host;
        return $this;
    }

    public function getPort(): ?int
    {
        return ($this->port !== null) ? (int) $this->port : null;
    }

    public function setPort(?int $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path ?? '/';
    }

    public function setPath(?string $path): self
    {
        $this->path = rtrim(($path ?? '/'), '/') . '/';
        return $this;
    }

    public function params(?string $key = null)
    {
        if ($key !== null) {
            return isset($this->params[$key]) ?
                $this->params[$key] : null;
        }

        return $this->params;
    }

    public function mergeParams(array $params): self
    {
        return $this->setParams(
            array_merge($this->params(), $params)
        );
    }

    public function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function setParam(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function hasParam(string $name): bool
    {
        return array_key_exists($name, $this->params());
    }

    public function removeParams(...$names): self
    {
        $params = array_diff_key(
            $this->params(),
            array_flip(...$names)
        );
        $this->setParams($params);
        return $this;
    }

    public function removeParam(string $name): self
    {
        $params = $this->params();
        unset($params[$name]);
        $this->setParams($params);
        return $this;
    }

    public function param(string $name,  ?string $defaultValue = null): ?string
    {
        return (isset($this->params()[$name]) === true) ?
            $this->params()[$name] : $defaultValue;
    }

    public function setQueryString(?string $queryString): self
    {
        if ($queryString !== null) {
            $params = [];
            parse_str($queryString, $params);
            if (count($params) > 0) {
                return $this->setParams($params);
            }
        }
        return $this;
    }

    public function getQueryString(bool $includeEmpty = true, ...$args): string
    {
        $getParams = $this->params();
        if (count($getParams) !== 0) {
            if ($includeEmpty === false) {
                $getParams = array_filter(
                    $getParams,
                    static function ($item) {
                        return (trim($item) !== '');
                    }
                );
            }
            return http_build_query($getParams, ...$args);
        }
        return '';
    }

    public function getFragment(): ?string
    {
        return $this->fragment;
    }

    public function setFragment(?string $fragment): self
    {
        $this->fragment = $fragment;
        return $this;
    }

    public function originalUrl(): string
    {
        return $this->originalUrl;
    }

    public function contains(string $value): bool
    {
        return stripos(
            trim($this->originalUrl(), '/') . '/',
            trim($value, '/') . '/'
        ) !== false;
    }

    public function is(string $value): bool
    {
        return trim($this->path, '/') === trim($value, '/');
    }

    public function relativeUrl(bool $includeParams = true): string
    {
        $path = $this->path ?? '/';
        if ($includeParams === false) {
            return $path;
        }
        $query    = $this->getQueryString() !== '' ? '?' . $this->getQueryString() : '';
        $fragment = $this->fragment !== null ? '#' . $this->fragment : '';
        return $path . $query . $fragment;
    }

    public function absoluteUrl(bool $includeParams = true): string
    {
        $scheme = sprintf('http%s://', $this->isSecure() ? 's' : '');
        $host   = $this->host ?? '';
        $port   = $this->port !== null ? ':' . $this->port : '';
        $user   = $this->username ?? '';
        $pass   = $this->password !== null ? ':' . $this->password : '';
        $pass   = ($user || $pass) ? $pass . '@' : '';
        return $scheme . $user . $pass . $host . $port . $this->relativeUrl($includeParams);
    }

    public function jsonSerialize(): string
    {
        return $this->relativeUrl();
    }

    public function __toString(): string
    {
        return $this->relativeUrl();
    }
}

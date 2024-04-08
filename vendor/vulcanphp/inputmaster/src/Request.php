<?php

namespace VulcanPhp\InputMaster;

use VulcanPhp\InputMaster\Input\InputHandler;

class Request
{
    const REQUEST_TYPE_GET          = 'get',
        REQUEST_TYPE_POST           = 'post',
        REQUEST_TYPE_PUT            = 'put',
        REQUEST_TYPE_PATCH          = 'patch',
        REQUEST_TYPE_OPTIONS        = 'options',
        REQUEST_TYPE_DELETE         = 'delete',
        REQUEST_TYPE_HEAD           = 'head',
        CONTENT_TYPE_JSON           = 'application/json',
        CONTENT_TYPE_FORM_DATA      = 'multipart/form-data',
        CONTENT_TYPE_X_FORM_ENCODED = 'application/x-www-form-urlencoded',
        FORCE_METHOD_KEY            = '_method';

    public static $requestTypes = [
        self::REQUEST_TYPE_GET,
        self::REQUEST_TYPE_POST,
        self::REQUEST_TYPE_PUT,
        self::REQUEST_TYPE_PATCH,
        self::REQUEST_TYPE_OPTIONS,
        self::REQUEST_TYPE_DELETE,
        self::REQUEST_TYPE_HEAD,
    ], $requestTypesPost = [
        self::REQUEST_TYPE_POST,
        self::REQUEST_TYPE_PUT,
        self::REQUEST_TYPE_PATCH,
        self::REQUEST_TYPE_DELETE,
    ];

    protected $headers, $contentType, $host, $url, $method, $inputHandler, $responseInstance;

    public function __construct()
    {
        $this->headers = [];

        foreach ($_SERVER as $key => $value) {
            $this->headers[str_replace('_', '-', strtolower($key))] = $value;
        }

        $this->setHost($this->header('http-host'));

        // Check if special IIS header exist, otherwise use default.
        $this->setUrl(
            new Url(
                urldecode($this->firstHeader(['unencoded-url', 'request-uri']))
            )
        );
        $this->setContentType(
            (string) $this->header('content-type')
        );
        $this->setMethod(
            (string) ($_POST[static::FORCE_METHOD_KEY] ?? $this->header('request-method'))
        );
        $this->responseInstance = new Response($this);
        $this->inputHandler = new InputHandler(
            $this->isPostBack()
        );
    }

    public function isSecure(): bool
    {
        return $this->header('http-x-forwarded-proto') === 'https' ||
            $this->header('https') !== null ||
            $this->header('server-port') === 443;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function rootUrl(): Url
    {
        return new Url(
            sprintf(
                '%s://%s',
                $this->header('https') === 'on' ? 'https' : 'http',
                $this->header('http-host')
            )
        );
    }

    public function isMethod($method): bool
    {
        return in_array($this->method, (array) $method);
    }

    public function user(): ?string
    {
        return $this->header('php-auth-user');
    }

    public function password(): ?string
    {
        return $this->header('php-auth-pw');
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function getIp(bool $safeMode = false): ?string
    {
        $headers = ['remote-addr'];
        if ($safeMode === false) {
            $headers = array_merge($headers, [
                'http-cf-connecting-ip',
                'http-client-ip',
                'http-x-forwarded-for',
            ]);
        }

        return $this->firstHeader($headers);
    }

    public function referer(): ?string
    {
        return $this->header('http-referer');
    }

    public function userAgent(): ?string
    {
        return $this->header('http-user-agent');
    }

    public function header(string $name, $defaultValue = null, bool $tryParse = true): ?string
    {
        $name   = str_replace('_', '-', strtolower($name));
        $header = $this->headers[$name] ?? null;

        if ($tryParse === true && $header === null) {
            if (strpos($name, 'http-') === 0) {
                // Trying to find client header variant which was not found, searching for header variant without http- prefix.
                $header = $this->headers[str_replace('http-', '', $name)] ?? null;
            } else {
                // Trying to find server variant which was not found, searching for client variant with http- prefix.
                $header = $this->headers['http-' . $name] ?? null;
            }
        }

        return $header ?? $defaultValue;
    }

    public function firstHeader(array $headers, $defaultValue = null)
    {
        foreach ($headers as $header) {
            $header = $this->header($header);
            if ($header !== null) {
                return $header;
            }
        }

        return $defaultValue;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    protected function setContentType(string $contentType): self
    {
        if (strpos($contentType, ';') > 0) {
            $this->contentType = strtolower(substr($contentType, 0, strpos($contentType, ';')));
        } else {
            $this->contentType = strtolower($contentType);
        }

        return $this;
    }

    public function isFormatAccepted(string $format): bool
    {
        return $this->header('http-accept') !== null &&
            stripos($this->header('http-accept'), $format) !== false;
    }

    public function isAjax(): bool
    {
        return strtolower($this->header('http-x-requested-with', '')) === 'xmlhttprequest';
    }

    public function isPostBack(): bool
    {
        return in_array(
            $this->getMethod(),
            static::$requestTypesPost,
            true
        );
    }

    public function acceptFormats(): array
    {
        return explode(',', $this->header('http-accept'));
    }

    public function setUrl(Url $url): void
    {
        $this->url = $url;

        if ($this->url->getHost() === null) {
            $this->url->setHost((string) $this->getHost());
        }

        if ($this->isSecure() === true) {
            $this->url->setScheme('https');
        }
    }

    public function inputHandler(): InputHandler
    {
        return $this->inputHandler;
    }

    public function getResponse(): Response
    {
        return $this->responseInstance;
    }

    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    public function setMethod(string $method): void
    {
        $this->method = strtolower($method);
    }
}

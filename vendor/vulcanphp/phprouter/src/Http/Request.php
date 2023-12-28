<?php

namespace VulcanPhp\PhpRouter\Http;

use VulcanPhp\PhpRouter\Callback\UniversalCall;
use VulcanPhp\PhpRouter\Security\BaseCsrfVerifier;
use VulcanPhp\PhpRouter\Http\Input\InputHandler;
use VulcanPhp\PhpRouter\Routing\IRoute;

class Request
{
    use UniversalCall;

    const REQUEST_TYPE_GET            = 'get';
    const REQUEST_TYPE_POST           = 'post';
    const REQUEST_TYPE_PUT            = 'put';
    const REQUEST_TYPE_PATCH          = 'patch';
    const REQUEST_TYPE_OPTIONS        = 'options';
    const REQUEST_TYPE_DELETE         = 'delete';
    const REQUEST_TYPE_HEAD           = 'head';
    const CONTENT_TYPE_JSON           = 'application/json';
    const CONTENT_TYPE_FORM_DATA      = 'multipart/form-data';
    const CONTENT_TYPE_X_FORM_ENCODED = 'application/x-www-form-urlencoded';
    const FORCE_METHOD_KEY            = '_method';

    /**
     * All request-types
     * @var string[]
     */
    public static $requestTypes = [
        self::REQUEST_TYPE_GET,
        self::REQUEST_TYPE_POST,
        self::REQUEST_TYPE_PUT,
        self::REQUEST_TYPE_PATCH,
        self::REQUEST_TYPE_OPTIONS,
        self::REQUEST_TYPE_DELETE,
        self::REQUEST_TYPE_HEAD,
    ];

    /**
     * Post request-types.
     * @var string[]
     */
    public static $requestTypesPost = [
        self::REQUEST_TYPE_POST,
        self::REQUEST_TYPE_PUT,
        self::REQUEST_TYPE_PATCH,
        self::REQUEST_TYPE_DELETE,
    ];

    /**
     * Server headers
     * @var array
     */
    protected $headers = [];

    /**
     * Request ContentType
     * @var string
     */
    protected $contentType;

    /**
     * Request host
     * @var string
     */
    protected $host;

    /**
     * Current request url
     * @var Url
     */
    protected $url;

    /**
     * Request method
     * @var string
     */
    protected $method;

    /**
     * Input handler
     * @var InputHandler
     */
    protected $inputHandler;

    /**
     * @var IRoute
     */
    protected IRoute $route;

    public function __construct()
    {
        foreach ($_SERVER as $key => $value) {
            $this->headers[str_replace('_', '-', strtolower($key))] = $value;
        }

        $this->setHost($this->header('http-host'));

        // Check if special IIS header exist, otherwise use default.
        $this->setUrl(new Url(urldecode($this->firstHeader(['unencoded-url', 'request-uri']))));
        $this->setContentType((string) $this->header('content-type'));
        $this->setMethod((string) ($_POST[static::FORCE_METHOD_KEY] ?? $this->header('request-method')));
        $this->inputHandler = new InputHandler($this->isPostBack());
    }

    public static function create(...$args): Request
    {
        return new Request(...$args);
    }

    public function isSecure(): bool
    {
        return $this->header('http-x-forwarded-proto') === 'https' || $this->header('https') !== null || $this->header('server-port') === 443;
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return string|null
     */
    public function getMethod(): ?string
    {
        return $this->method;
    }

    /**
     * @return Url
     */
    public function rootUrl(): Url
    {
        $url = sprintf(
            '%s://%s',
            $this->header('https') === 'on' ? 'https' : 'http',
            $this->header('http-host')
        );
        return new Url($url);
    }

    /**
     * Methods to Match with current request
     * @param mixed $method
     * @return bool
     */
    public function isMethod($method): bool
    {
        return in_array($this->method, (array) $method);
    }

    /**
     * Get http basic auth user
     * @return string|null
     */
    public function user(): ?string
    {
        return $this->header('php-auth-user');
    }

    /**
     * Get http basic auth password
     * @return string|null
     */
    public function password(): ?string
    {
        return $this->header('php-auth-pw');
    }

    /**
     * Get the csrf token
     * @return string|null
     */
    public function csrfToken(): ?string
    {
        return $this->header(BaseCsrfVerifier::HEADER_KEY);
    }

    /**
     * Get all headers
     * @return array
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get id address
     * If $safe is false, this function will detect Proxys. But the user can edit this header to whatever he wants!
     * https://stackoverflow.com/questions/3003145/how-to-get-the-client-ip-address-in-php#comment-25086804
     * @param bool $safeMode When enabled, only safe non-spoofable headers will be returned. Note this can cause issues when using proxy.
     * @return string|null
     */
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

    /**
     * Get remote address/ip
     *
     * @alias static::getIp
     * @return string|null
     */
    public function remoteAddr(): ?string
    {
        return $this->getIp();
    }

    /**
     * Get referer
     * @return string|null
     */
    public function referer(): ?string
    {
        return $this->header('http-referer');
    }

    /**
     * Get user agent
     * @return string|null
     */
    public function userAgent(): ?string
    {
        return $this->header('http-user-agent');
    }

    /**
     * Get header value by name
     *
     * @param string $name Name of the header.
     * @param string|mixed|null $defaultValue Value to be returned if header is not found.
     * @param bool $tryParse When enabled the method will try to find the header from both from client (http) and server-side variants, if the header is not found.
     *
     * @return string|null
     */
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

    /**
     * Will try to find first header from list of headers.
     *
     * @param array $headers
     * @param mixed|null $defaultValue
     * @return mixed|null
     */
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

    /**
     * Get request content-type
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Set request content-type
     * @param string $contentType
     * @return $this
     */
    protected function setContentType(string $contentType): self
    {
        if (strpos($contentType, ';') > 0) {
            $this->contentType = strtolower(substr($contentType, 0, strpos($contentType, ';')));
        } else {
            $this->contentType = strtolower($contentType);
        }

        return $this;
    }

    /**
     * Is format accepted
     *
     * @param string $format
     *
     * @return bool
     */
    public function isFormatAccepted(string $format): bool
    {
        return ($this->header('http-accept') !== null && stripos($this->header('http-accept'), $format) !== false);
    }

    /**
     * Returns true if the request is made through Ajax
     *
     * @return bool
     */
    public function isAjax(): bool
    {
        return (strtolower($this->header('http-x-requested-with', '')) === 'xmlhttprequest');
    }

    /**
     * Returns true when request-method is type that could contain data in the page body.
     *
     * @return bool
     */
    public function isPostBack(): bool
    {
        return in_array($this->getMethod(), static::$requestTypesPost, true);
    }

    /**
     * Get accept formats
     * @return array
     */
    public function acceptFormats(): array
    {
        return explode(',', $this->header('http-accept'));
    }

    /**
     * @param Url $url
     */
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

    public function setRoute(IRoute $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function getRoute(): IRoute
    {
        return $this->route;
    }

    /**
     * Get input class
     * @return InputHandler
     */
    public function inputHandler(): InputHandler
    {
        return $this->inputHandler;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = strtolower($method);
    }
}

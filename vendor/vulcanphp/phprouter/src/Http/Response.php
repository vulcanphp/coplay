<?php

namespace VulcanPhp\PhpRouter\Http;

use JsonSerializable;
use VulcanPhp\PhpRouter\Http\Exceptions\InvalidArgumentException;
use VulcanPhp\PhpRouter\Router;

class Response
{
    public function __construct()
    {
    }

    public static function create(...$args): Response
    {
        return new Response(...$args);
    }

    /**
     * Set the http status code
     *
     * @param int $code
     * @return static
     */
    public function httpCode(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    /**
     * Redirect the response
     *
     * @param string $url
     * @param ?int $httpCode
     */
    public function redirect(string $url,  int $httpCode = 0): void
    {
        if ($httpCode != 0 && $httpCode !== 200) {
            header('Location: ' . $url, true, $httpCode);
        } else {
            $this->header('location: ' . $url);
        }
        exit;
    }

    /**
     * Redirect the response
     *
     * @param string $url
     * @param ?int $httpCode
     */
    public function back($suffix = '')
    {
        return $this->redirect(Router::$instance->request->referer() . $suffix);
    }

    public function refresh(): void
    {
        $this->redirect(Router::$instance->request->getUrl()->originalUrl());
    }

    /**
     * Add http authorisation
     * @param string $name
     * @return static
     */
    public function auth(string $name = ''): self
    {
        $this->headers([
            'WWW-Authenticate: Basic realm="' . $name . '"',
            'HTTP/1.0 401 Unauthorized',
        ]);

        return $this;
    }

    public function cache(string $eTag, int $lastModifiedTime = 2592000): self
    {
        $this->headers([
            'Cache-Control: public',
            sprintf('Last-Modified: %s GMT', gmdate('D, d M Y H:i:s', $lastModifiedTime)),
            sprintf('Etag: %s', $eTag),
        ]);

        $httpModified    = Router::$instance->request->header('http-if-modified-since');
        $httpIfNoneMatch = Router::$instance->request->header('http-if-none-match');

        if (($httpIfNoneMatch !== null && $httpIfNoneMatch === $eTag) || ($httpModified !== null && strtotime($httpModified) === $lastModifiedTime)) {

            $this->header('HTTP/1.1 304 Not Modified');
            exit(0);
        }

        return $this;
    }

    /**
     * Json encode
     * @param array|JsonSerializable $value
     * @param ?int $options JSON options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR.
     * @param int $dept JSON debt.
     * @throws InvalidArgumentException
     */
    public function json(array $value,  int $options =  JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, int $dept = 512)
    {
        if (($value instanceof JsonSerializable) === false && is_array($value) === false) {
            throw new InvalidArgumentException('Invalid type for parameter "value". Must be of type array or object implementing the \JsonSerializable interface.');
        }

        $this->header('Content-Type: application/json; charset=utf-8');

        return json_encode($value, $options, $dept);
    }

    /**
     * Add header to response
     * @param string $value
     * @return static
     */
    public function header($value): self
    {
        if (is_array($value)) {
            $value = sprintf('%s: %s', key($value), array_values($value)[0]);
        }

        header($value);

        return $this;
    }

    /**
     * Add multiple headers to response
     * @param array $headers
     * @return static
     */
    public function headers(array $headers): self
    {
        foreach ($headers as $key => $header) {
            $this->header(!intval($key) == $key ? [$key => $header] : $header);
        }

        return $this;
    }
}

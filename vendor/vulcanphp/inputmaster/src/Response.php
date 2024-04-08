<?php

namespace VulcanPhp\InputMaster;

use JsonSerializable;
use VulcanPhp\InputMaster\Exceptions\InvalidArgumentException;

class Response
{
    protected Request $request;
    protected array $body;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->body = [];
    }

    public function httpCode(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    public function write(...$args): self
    {
        if (func_num_args() == 2) {
            $this->body[$args[0]] = $args[1];
        } else {
            $this->body[] = $args[0];
        }

        return $this;
    }

    public function output(): void
    {
        echo join("\n", $this->body);
        exit;
    }

    public function redirect(string $url,  int $httpCode = 0): void
    {
        header("Location: $url", true, $httpCode);
        exit;
    }

    public function back($suffix = '')
    {
        return $this->redirect(
            $this->request->referer() . $suffix
        );
    }

    public function refresh(): void
    {
        $this->redirect(
            $this->request->getUrl()->originalUrl()
        );
    }

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

        $httpModified    = $this->request->header('http-if-modified-since');
        $httpIfNoneMatch = $this->request->header('http-if-none-match');

        if (
            ($httpIfNoneMatch !== null && $httpIfNoneMatch === $eTag) ||
            ($httpModified !== null && strtotime($httpModified) === $lastModifiedTime)
        ) {
            $this->header('HTTP/1.1 304 Not Modified');
            exit(0);
        }

        return $this;
    }

    public function json(array $value,  int $options =  JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, int $dept = 512): void
    {
        if (($value instanceof JsonSerializable) === false && is_array($value) === false) {
            throw new InvalidArgumentException('Invalid type for parameter "value". Must be of type array or object implementing the \JsonSerializable interface.');
        }

        $this->header('Content-Type: application/json; charset=utf-8');

        echo json_encode($value, $options, $dept);
        exit;
    }

    public function header($value): self
    {
        if (is_array($value)) {
            $value = sprintf(
                '%s: %s',
                key($value),
                array_values($value)[0]
            );
        }

        header($value);

        return $this;
    }

    public function headers(array $headers): self
    {
        foreach ($headers as $key => $header) {
            $this->header(
                !intval($key) == $key ?
                    [$key => $header] : $header
            );
        }

        return $this;
    }
}

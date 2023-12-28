<?php

namespace VulcanPhp\PhpRouter\Security;

use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Security\Exceptions\TokenMismatchException;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;
use VulcanPhp\PhpRouter\Security\Interfaces\ITokenProvider;

class BaseCsrfVerifier implements IMiddleware
{
    const POST_KEY   = '_token';
    const HEADER_KEY = 'X-CSRF-TOKEN';

    /**
     * Urls to ignore. You can use * to exclude all sub-urls on a given path.
     * For example: /admin/*
     * @var array|null
     */
    protected $except;

    /**
     * Urls to include. Can be used to include urls from a certain path.
     * @var array|null
     */
    protected $include;

    /**
     * BaseCsrfVerifier constructor.
     */
    protected ITokenProvider $tokenProvider;

    /**
     * Check if the url matches the urls in the except property
     * @param Request $request
     * @return bool
     */
    protected function skip(Request $request): bool
    {
        if ($this->except === null || count($this->except) === 0) {
            return false;
        }

        foreach ($this->except as $url) {
            $url = rtrim($url, '/');
            if ($url[strlen($url) - 1] === '*') {
                $url  = rtrim($url, '*');
                $skip = $request->getUrl()->contains($url);
            } else {
                $skip = ($url === rtrim($request->getUrl()->relativeUrl(false), '/'));
            }

            if ($skip === true) {

                if (is_array($this->include) === true && count($this->include) > 0) {
                    foreach ($this->include as $includeUrl) {
                        $includeUrl = rtrim($includeUrl, '/');
                        if ($includeUrl[strlen($includeUrl) - 1] === '*') {
                            $includeUrl = rtrim($includeUrl, '*');
                            $skip       = !$request->getUrl()->contains($includeUrl);
                            break;
                        }

                        $skip = !($includeUrl === rtrim($request->getUrl()->relativeUrl(false), '/'));
                    }
                }

                if ($skip === false) {
                    continue;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Handle request
     *
     * @throws TokenMismatchException
     */
    public function handle(Request $request, Response $response): void
    {
        if ($request->isPostBack() === true && $this->skip($request) === false) {
            $token = $request->inputHandler()
                ->value(
                    static::POST_KEY,
                    $request->header(static::HEADER_KEY),
                    Request::$requestTypes
                );

            if ($this->tokenProvider->validate((string) $token) === false) {
                throw new TokenMismatchException('Invalid CSRF-token.');
            }
        }

        // Refresh existing token
        $this->tokenProvider->refresh();
    }

    public function getTokenProvider(): ITokenProvider
    {
        return $this->tokenProvider;
    }

    /**
     * Set token provider
     * @param ITokenProvider $provider
     */
    public function setTokenProvider(ITokenProvider $provider): void
    {
        $this->tokenProvider = $provider;
    }
}

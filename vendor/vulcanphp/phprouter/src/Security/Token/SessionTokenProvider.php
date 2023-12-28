<?php

namespace VulcanPhp\PhpRouter\Security\Token;

use Exception;
use VulcanPhp\PhpRouter\Security\Exceptions\SecurityException;
use VulcanPhp\PhpRouter\Security\Interfaces\ITokenProvider;

class SessionTokenProvider implements ITokenProvider
{
    const CSRF_KEY = 'CSRF-TOKEN';
    protected $token;

    /**
     * CookieTokenProvider constructor.
     * @throws SecurityException
     */
    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->token = $_SESSION[static::CSRF_KEY] ?? null;

        if ($this->token === null) {
            $this->token = $this->generateToken();
        }
    }

    /**
     * Generate random identifier for CSRF token
     *
     * @return string
     * @throws SecurityException
     */
    public function generateToken(): string
    {
        try {
            return bin2hex(random_bytes(32));
        } catch (Exception $e) {
            throw new SecurityException($e->getMessage(), (int) $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * Validate valid CSRF token
     *
     * @param string $token
     * @return bool
     */
    public function validate(string $token): bool
    {
        if ($this->getToken() !== null) {
            return hash_equals($token, $this->getToken());
        }

        return false;
    }

    /**
     * Set csrf token cookie
     * Overwrite this method to save the token to another storage like session etc.
     *
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
        $_SESSION[static::CSRF_KEY] = $this->token;
    }

    /**
     * Get csrf token
     * @param string|null $defaultValue
     * @return string|null
     */
    public function getToken(?string $defaultValue = null): ?string
    {
        return $this->token ?? $defaultValue;
    }

    /**
     * Refresh existing token
     */
    public function refresh(): self
    {
        if ($this->token !== null) {
            $this->setToken($this->token);
        }

        return $this;
    }
}

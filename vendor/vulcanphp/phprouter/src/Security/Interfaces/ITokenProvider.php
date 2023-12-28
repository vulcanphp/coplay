<?php

namespace VulcanPhp\PhpRouter\Security\Interfaces;

interface ITokenProvider
{
    /**
     * Refresh existing token
     */
    public function refresh(): self;

    /**
     * Validate valid CSRF token
     *
     * @param string $token
     * @return bool
     */
    public function validate(string $token): bool;

    /**
     * Get token token
     *
     * @param string|null $defaultValue
     * @return string|null
     */
    public function getToken(?string $defaultValue = null): ?string;
}

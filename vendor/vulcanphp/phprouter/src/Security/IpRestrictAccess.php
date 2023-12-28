<?php

namespace VulcanPhp\PhpRouter\Security;

use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Security\Exceptions\RestrictAccessException;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;

abstract class IpRestrictAccess implements IMiddleware
{
    protected $ipBlacklist = [];
    protected $ipWhitelist = [];

    protected function validate(string $ip): bool
    {
        // Accept ip that is in white-list
        if (in_array($ip, $this->ipWhitelist, true) === true) {
            return true;
        }

        foreach ($this->ipWhitelist as $whiteIp) {
            // Blocks range (8.8.*)
            if ($whiteIp[strlen($whiteIp) - 1] === '*' && strpos($ip, trim($whiteIp, '*')) === 0) {
                return true;
            }
        }

        foreach ($this->ipBlacklist as $blackIp) {

            if ($blackIp === '*') {
                return false;
            }

            // Blocks range (8.8.*)
            if ($blackIp[strlen($blackIp) - 1] === '*' && strpos($ip, trim($blackIp, '*')) === 0) {
                return false;
            }

            // Blocks exact match
            if ($blackIp === $ip) {
                return false;
            }
        }

        return true;
    }

    /**
     * @throws RestrictAccessException
     */
    public function handle(Request $request, Response $response): void
    {
        if ($this->validate((string) $request->getIp()) === false) {
            throw new RestrictAccessException(sprintf('Restricted ip. Access to %s has been blocked', $request->getIp()), 403);
        }
    }
}

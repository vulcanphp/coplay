<?php

namespace VulcanPhp\Core\Helpers;

/**
 * @link https://github.com/bayfrontmedia/php-cookies
 */
class Cookie
{
    public static function get(?string $key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }

        return $default;
    }

    public static function has(string $key): bool
    {
        return (self::get($key)) ? true : false;
    }

    public static function set(string $name, string $value, int $minutes = 0, string $path = '/', string $domain = '', bool $secure = true, bool $http_only = true): bool
    {
        // < 0 = Delete, 0 = Expire at end of session, > 0 = Minutes from now
        if ($minutes < 0) {
            $time = time() - 3600;
        } else if ($minutes == 0) {
            $time = time();
        } else {
            $time = time() + 60 * $minutes;
        }

        $set = setcookie($name, $value, [
            'expires'  => $time,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $http_only
        ]);

        if ($set) {
            $_COOKIE[$name] = $value; // Make available without reloading page
            return true;
        }

        return false;
    }

    public static function remove(string $name): void
    {
        self::set($name, '', -1, '/'); // Expire in browser
        unset($_COOKIE[$name]); // Remove from script
    }

    public static function removeAll(): void
    {
        $cookies = self::get();

        if (is_array($cookies)) {
            foreach ($cookies as $cookie => $value) {
                self::remove($cookie);
            }
        }
    }
}

<?php

namespace VulcanPhp\Core\Helpers;

class Session
{
    public const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        $this->start();
    }

    public static function create(): Session
    {
        return new Session;
    }

    public static function start(): void
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function ID()
    {
        return session_id();
    }

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = ['remove' => false, 'value' => $message];
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION) && !empty($_SESSION[$key]);
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function flush(): void
    {
        session_unset();
        session_destroy();
    }

    public static function hasFlash(string $key): bool
    {
        $flash = $_SESSION[self::FLASH_KEY][$key] ?? null;

        return $flash !== null && $flash['remove'] !== true && !empty($flash['value']);
    }

    public static function getFlash(string $key): ?string
    {
        if (self::hasFlash($key)) {
            $_SESSION[self::FLASH_KEY][$key]['remove'] = true;
            return $_SESSION[self::FLASH_KEY][$key]['value'];
        }

        return null;
    }

    public static function compare($key, $expected, $strict = true)
    {
        $session = self::get($key);

        if ($strict) {
            return $session === $expected;
        }

        return $session == $expected;
    }

    public function __destruct()
    {
        $flash_messages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flash_messages as $key => &$flash_message) {
            if ($flash_message['remove']) {
                unset($flash_messages[$key]);
            }
        }

        $_SESSION[self::FLASH_KEY] = $flash_messages;
    }
}

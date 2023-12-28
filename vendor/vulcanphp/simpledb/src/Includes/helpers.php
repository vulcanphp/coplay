<?php

use VulcanPhp\SimpleDb\Database;
use VulcanPhp\SimpleDb\Includes\Paginator\Paginator;
use VulcanPhp\SimpleDb\Query;

if (!function_exists('database_init')) {
    function database_init(array $config): Database
    {
        return Database::init($config);
    }
}

if (!function_exists('db_init')) {
    function db_init(...$args): Database
    {
        return database_init(...$args);
    }
}

if (!function_exists('database_create')) {
    function database_create(array $config): Database
    {
        return Database::create($config);
    }
}

if (!function_exists('db_create')) {
    function db_create(...$args): Database
    {
        return database_create(...$args);
    }
}

if (!function_exists('database')) {
    function database(?array $config = null): Database
    {
        if ($config !== null) {
            database_init($config);
        }

        return Database::$instance;
    }
}

if (!function_exists('db')) {
    function db(...$args): Database
    {
        return database(...$args);
    }
}

if (!function_exists('database_hooks')) {
    function database_hooks()
    {
        return database()
            ->getHookHandler();
    }
}

if (!function_exists('db_hooks')) {
    function db_hooks()
    {
        return database_hooks();
    }
}

if (!function_exists('pdo')) {
    function pdo(): PDO
    {
        return database()
            ->getPdo();
    }
}

if (!function_exists('qb_table')) {
    function qb_table(...$args): Query
    {
        return Query::table(...$args);
    }
}

if (!function_exists('qb')) {
    function qb(...$args): Query
    {
        return qb_table(...$args);
    }
}

if (!function_exists('reset_pdo')) {
    function reset_pdo(): PDO
    {
        return database()
            ->resetPdo()
            ->getPdo();
    }
}

if (!function_exists('prepare')) {
    function prepare(string $sql)
    {
        return database()
            ->prepare($sql);
    }
}

if (!function_exists('is_pdo_driver')) {
    function is_pdo_driver(string $driver): bool
    {
        return strtolower(database()->config('driver')) === strtolower($driver);
    }
}

if (!function_exists('is_sqlite')) {
    function is_sqlite(): bool
    {
        return is_pdo_driver('sqlite');
    }
}

if (!function_exists('is_mysql')) {
    function is_mysql(): bool
    {
        return is_pdo_driver('mysql');
    }
}

if (!function_exists('paginator')) {
    function paginator(...$args): Paginator
    {
        return Paginator::create(...$args);
    }
}

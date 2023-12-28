<?php

namespace VulcanPhp\SimpleDb;

use PDO;
use PDOException;
use VulcanPhp\SimpleDb\Exceptions\DatabaseException;
use VulcanPhp\SimpleDb\Includes\HookHandler;

class Database
{
    public static Database $instance;

    protected PDO $pdo;
    protected HookHandler $hookHandler;
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->hookHandler = new HookHandler;

        unset($this->pdo);
    }

    public static function create(...$args): Database
    {
        return new Database(...$args);
    }

    public static function init(...$args): Database
    {
        return Database::$instance = Database::create(...$args);
    }

    public function getPdo(): PDO
    {
        if (!isset($this->pdo)) {
            $this->resetPdo();
        }

        return $this->getHookHandler()
            ->applyFilters('pdo', $this->pdo);
    }

    public function config(?string $key = null, $default = null)
    {
        return $key !== null ? ($this->config[$key] ?? $default) : $this->config;
    }

    public function beginTransaction()
    {
        $this->getHookHandler()
            ->doAction('transaction');

        $this->getPdo()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $this->getPdo()->beginTransaction();
    }

    public function commit()
    {
        $this->getHookHandler()
            ->doAction('commit');

        $this->getPdo()->commit();
        $this->getPdo()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    public function rollBack()
    {
        $this->getHookHandler()
            ->doAction('rollback');

        $this->getPdo()->rollBack();
        $this->getPdo()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    public function getHookHandler(): HookHandler
    {
        return $this->hookHandler;
    }

    public function prepare(string $query)
    {
        $this->getHookHandler()
            ->doAction('prepare', $query);

        return $this->getPdo()
            ->prepare(
                $this->getHookHandler()
                    ->applyFilters('prepare', $query)
            );
    }

    public function __call($name, array $args)
    {
        if ($this->getHookHandler()->hasFallback('database', $name)) {
            return $this->getHookHandler()
                ->getFallback('database', $name, $this, ...$args);
        }

        try {
            return call_user_func_array([$this->getPdo(), $name], $args);
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }
    }

    public function last_id(): int
    {
        return $this->getHookHandler()
            ->applyFilters('last_id', $this->getPdo()->lastInsertId());
    }

    // The ping() will try to reconnect once if connection lost.
    public function ping()
    {
        $this->getHookHandler()
            ->doAction('ping');

        try {
            $this->getPdo()->query('SELECT 1');
        } catch (PDOException $e) {
            $this->resetPdo();
        }

        return true;
    }

    public function resetPdo(): self
    {
        $this->getHookHandler()
            ->doAction('before_pdo');

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => true,
        ];

        if ($this->config('driver') === 'mysql') {
            $options[PDO::MYSQL_ATTR_INIT_COMMAND] = sprintf(
                "SET NAMES '%s' COLLATE '%s';",
                $this->config('charset', 'utf8mb4'),
                $this->config('collate', 'utf8mb4_unicode_ci')
            );
        }

        if ($this->config('driver') === 'sqlite') {
            if ($this->config('file') === null) {
                throw new DatabaseException('SQLite (file) does not specified');
            }

            $dsn = sprintf('sqlite:%s', $this->config('file'));
        } else {
            $dsn = sprintf(
                '%s:host=%s;port=%s;dbname=%s;charset=%s;',
                $this->config('driver'),
                $this->config('host', ''),
                $this->config('port', ''),
                $this->config('name'),
                $this->config('charset', 'utf8mb4'),
            );
        }

        try {
            $this->pdo = new PDO(
                // filter pdo dsn string
                $this->getHookHandler()
                    ->applyFilters('dsn', $dsn),

                // database access user
                $this->config('user', ''),

                // database access password
                $this->config('password', ''),

                // filter pdo options
                $this->getHookHandler()
                    ->applyFilters('pdo_options', $options)
            );
        } catch (PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e->getCode());
        }

        $this->getHookHandler()
            ->doAction('after_pdo', $this->pdo);

        return $this;
    }
}

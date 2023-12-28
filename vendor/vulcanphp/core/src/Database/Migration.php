<?php

namespace VulcanPhp\Core\Database;

use VulcanPhp\Core\Database\Interfaces\IMigration;
use VulcanPhp\Core\Database\Schema\Schema;
use PDO;

class Migration
{
    public function __construct(protected PDO $pdo, protected string $schemapath)
    {
    }

    public function applyMigrations(): void
    {
        $this->createMigrationsTable();

        $newMigrations     = [];
        $migrations        = scandir($this->schemapath);
        $toApplyMigrations = array_diff($migrations, $this->getAppliedMigrations());

        foreach ($toApplyMigrations as $migration) {

            if (pathinfo($migration, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

			$filepath = $this->schemapath . DIRECTORY_SEPARATOR . $migration;

            if (!file_exists($filepath)) {
                $this->log("ALERT! " . $migration . " This File is Does Not Exist");
                continue;
            }

            $instance = require $filepath;

            if (!$instance instanceof IMigration) {
                $this->log("ALERT! " . $className . " This Class Must be Implement " . IMigration::class);
                continue;
            }

            $this->log('Applyling Migration ' . $migration);

            try {
                $this->pdo->exec($instance->up());
            } catch (\Exception $e) {
                $this->log('migration error: ' . $e->getMessage());
                exit;
            }

            $this->log('Done Migration ' . $migration);

            $newMigrations[] = $migration;
        }

        if (!empty($newMigrations)) {
            $this->saveMigrations($newMigrations);
        } else {
            $this->log("Nothing to Migrate");
        }
    }

    public function applyRollback(): void
    {
        $lastmigrate 	= $this->getLastMigration();
        $filepath    	= $this->schemapath . DIRECTORY_SEPARATOR . $lastmigrate;

        if (empty($lastmigrate) || !file_exists($filepath)) {
            $this->log("Nothing to Rollback");
            exit;
        }

        $instance = require $filepath;

        $this->log('Rolling Back ' . $lastmigrate);

        try {
            $this->pdo->exec($instance->down());
        } catch (\Exception $e) {
            $this->log('rolling back error: ' . $e->getMessage());
            exit;
        }

        $this->removeMigration($lastmigrate);

        $this->log('Done Rollback ' . $lastmigrate);
    }

    protected function createMigrationsTable()
    {
        $this->pdo->exec(
            Schema::create('migrations')
                ->id()
                ->string('migration')->nullable()
                ->timestamp('created_at')
                ->build()
        );
    }

    protected function getAppliedMigrations()
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations");
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    protected function getLastMigration(int $step = 1)
    {
        $statement = $this->pdo->prepare("SELECT migration FROM migrations ORDER BY id DESC LIMIT " . $step);
        $statement->execute();

        return $statement->fetch(\PDO::FETCH_COLUMN);
    }

    protected function removeMigration($migration)
    {
        $this->pdo->exec("DELETE FROM migrations WHERE migration = '{$migration}'");
    }

    protected function saveMigrations(array $migrations)
    {
        $newMogrations = implode(',', array_map(fn ($m) => "('$m')", $migrations));
        $statement     = $this->pdo->prepare("INSERT INTO migrations (migration) VALUES $newMogrations");
        $statement->execute();
    }

    protected function log($message)
    {
        echo $message . ' - [' . date('Y-m-d H:i:s') . ']' . (php_sapi_name() == "cli" ? PHP_EOL : '<br/>');
    }
}

<?php

namespace VulcanPhp\Core\Database;

use VulcanPhp\Core\Database\Interfaces\ISeeder;

class Seeder
{
    public function __construct(protected string $seedpath)
    {
    }

    public function applySeeds(): void
    {
        foreach (scandir($this->seedpath) as $seed) {

            if (pathinfo($seed, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

			$filepath = $this->seedpath . DIRECTORY_SEPARATOR . $seed;

            if (!file_exists($filepath)) {
                $this->log("ALERT! " . $seed . " This File is Does Not Exist");
                continue;
            }

            $instance = require $filepath;

            if ((boolval($instance?->autoseed ?? true) !== true) || !$instance instanceof ISeeder) {
                $this->log('Ignored Seeding ' . $seed);
                continue;
            }

            $this->log('Applyling Seader ' . $seed);

            try {
                $instance->seed();
            } catch (\Exception $e) {
                $this->log('seeding error: ' . $e->getMessage());
                exit;
            }

            $this->disableSeeding($filepath);

            $this->log('Done Seeding ' . $seed);
        }
    }

    public function disableSeeding(string $filepath): bool
    {
        if (!is_writable($filepath) && !chmod($filepath, 0777)) {
            return false;
        }

        return file_put_contents($filepath, str_ireplace(['$autoseed = true;'], ['$autoseed = false;'], file_get_contents($filepath)), LOCK_EX);
    }

    private function log($message): void
    {
        echo $message . ' - [' . date('Y-m-d H:i:s') . ']' . (php_sapi_name() == "cli" ? PHP_EOL : '<br/>');
    }
}

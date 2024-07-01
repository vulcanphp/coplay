<?php

namespace VulcanPhp\Core\Console;

use VulcanPhp\Core\Database\Migration;
use VulcanPhp\Core\Database\Seeder;
use VulcanPhp\SimpleDb\Database;

class Callback
{
    public function serve(array $args)
    {
        $port = $args['flags'][0] ?? 8080;
        echo sprintf(
            "\n\033[36mApplication is running:\033[0m \033[32mhttp://127.0.0.1:%s or http://localhost:%s\033[0m\nPress \033[33mCtrl+C\033[0m to Stop Running this Application.\n\n",
            $port,
            $port
        );

        exec('php -S 127.0.0.1:' . $port);
    }

    public function help()
    {
        echo sprintf(
            "\n\033[36mphp vulcan is a cli to add new feature to VulcanPhp application.\033[0m\nhere is all the available commands:\n\n"
        );

        foreach (require __DIR__ . '/routes.php' as $command) {
            echo sprintf(
                "    \033[32m%s\033[0m to \033[36m%s.\033[0m\n",
                join(', ', (array) $command['command']) . (isset($command['action']) ? ':' . join(', ', (array) $command['action']) : ''),
                $command['info']
            );
        }
    }

    public function tailwindMinify()
    {
        exec('npx tailwindcss -i ./resources/assets/css/app.css -o ./resources/assets/dist/bundle.min.css --minify');
    }

    public function tailwindWatch()
    {
        exec('npx tailwindcss -i ./resources/assets/css/app.css -o ./resources/assets/dist/bundle.min.css --watch');
    }

    public function tailwindInit()
    {
        $config = root_dir('tailwind.config.js');
        if (file_put_contents($config, <<<EOT
            /** @type {import('tailwindcss').Config} */
            module.exports = {
                content: [
                    "./resources/views/**/*.{php,js,html}",
                ],
                theme: {
                    container: {
                        center: true,
                        padding: '1rem',
                        screens: {},
                    },
                    extend: {
                        colors: {}
                    },
                },
            }
        EOT)) {
            $this->log("Success, {$config} has been created.", 'success');
        } else {
            $this->log("Failed to create: {$config}", 'error');
        }
    }

    public function table($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Migration name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->migration($action);
    }

    public function seeder($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Seeder name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->seeder($action);
    }

    public function controller($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Controller name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->controller($action, in_array('-r', $flags));
    }

    public function model($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Model name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->model($action);
    }

    public function middleware($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Middleware name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->middleware($action);
    }

    public function kernel($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Kernel name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->kernel($action);
    }

    public function view($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('View name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->view($action);
    }

    public function mail($args)
    {
        $flags = $args['flags'] ?? [];

        if (!isset($flags[0])) {
            return $this->log('Mail Template name does not found.', 'warning');
        }

        $action = array_shift($flags);

        $this->Create()->mail($action);
    }

    public function resource($args)
    {
        $args['flags'][] = '-r';

        $this->log('Creating Resource For: ' . $args['flags'][0], 'info');
        $this->table($args);
        $this->seeder($args);
        $this->controller($args);
        $this->model($args);

        foreach (['index', 'create', 'show', 'edit'] as $view) {
            $view_args = $args;
            $view_args['flags'][0] = $view_args['flags'][0] . '/' . $view;

            $this->view($view_args);
        }

        $this->log('Resource:: ' . $args['flags'][0] . ' has been created.', 'success');
    }

    public function migrate()
    {
        (new Migration(Database::$instance->getPdo(), root_dir('/database/migrations/')))->applyMigrations();
    }

    public function rollback()
    {
        (new Migration(Database::$instance->getPdo(), root_dir('/database/migrations/')))->applyRollback();
    }

    public function seed()
    {
        (new Seeder(root_dir('/database/seeders/')))->applySeeds();
    }

    protected function Create(): Create
    {
        return new Create;
    }

    protected function log($message, $type = null)
    {
        echo match ($type) {
            'error' => "\n\033[31m$message \033[0m\n",
            'success' => "\n\033[32m$message \033[0m\n",
            'warning' => "\n\033[33m$message \033[0m\n",
            'info' => "\n\033[36m$message \033[0m\n",
            default => $message
        };
    }
}

<?php

namespace VulcanPhp\Core\Console;

class Create
{
    public function migration(string $name): void
    {
        $location   = root_dir('/database/migrations/');
        $name       = explode('/', $name);
        $name       = array_pop($name);
        $serial     = $this->TotalExistedFiles($location) + 1;
        $filename   = sprintf('m%s_create_%s', sprintf("%03d", $serial), strtolower($name));
        $filepath   = $this->CheckDirLocation($location) . $filename . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{TableName}'], [strtolower($name)], $this->getSchema('migration'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $filename . ' Migration has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create Migration ' . $filename, 'error');
            }
        } else {
            $this->log('Migration: ' . $filename . ' is already exists', 'info');
        }
    }

    public function seeder(string $name): void
    {
        $location   = root_dir('/database/seeders/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $serial     = $this->TotalExistedFiles($location) + 1;
        $filename   = sprintf('s%s_%s', sprintf("%03d", $serial), strtolower($name));
        $filepath   = $this->CheckDirLocation($location) . $filename . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{ModelName}', '{Namespane}'], [$name, $this->joinFolders($div) . DIRECTORY_SEPARATOR . $name], $this->getSchema('seeder'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $filename . ' Seeder has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create Seeder ' . $filename, 'error');
            }
        } else {

            $this->log('Seeder: ' . $filename . ' is already exists', 'info');
        }
    }

    public function controller(string $name, bool $resource = false): void
    {
        $location   = root_dir('/app/Http/Controllers/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . $name . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{ControllerName}', '{Namespace}'], [$name, rtrim('\\' . $this->joinFolders($div, '\\'), '\\')], $this->getSchema($resource ? 'resource_controller' : 'controller'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $name . ' Controller has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name, 'error');
            }
        } else {
            $this->log('Controller: ' . $name . ' is already exists', 'info');
        }
    }

    public function model(string $name): void
    {
        $location   = root_dir('/app/Models/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . $name . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{ModelName}', '{TableName}', '{Namespace}'], [$name, strtolower($name), rtrim('\\' . $this->joinFolders($div, '\\'), '\\')], $this->getSchema('model'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $name . ' Model has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name . ' Model', 'error');
            }
        } else {
            $this->log('Model: ' . $name . ' is already exists', 'info');
        }
    }

    public function middleware(string $name): void
    {
        $location   = root_dir('/app/Http/Middlewares/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . $name . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{MiddlewareName}', '{Namespace}'], [$name, rtrim('\\' . $this->joinFolders($div, '\\'), '\\')], $this->getSchema('middleware'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $name . ' Middleware has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name . ' Middleware', 'error');
            }
        } else {
            $this->log('Middleware: ' . $name . ' is already exists', 'info');
        }
    }

    public function kernel(string $name): void
    {
        $location   = root_dir('/app/Http/Kernels/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR . $name . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            $schema = str_ireplace(['{KernelName}', '{Namespace}'], [$name, rtrim('\\' . $this->joinFolders($div, '\\'), '\\')], $this->getSchema('kernel'));
            if (file_put_contents($filepath, $schema, LOCK_EX)) {
                $this->log('Done: ' . $name . ' Kernel has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name . ' Kernel', 'error');
            }
        } else {
            $this->log('Kernel: ' . $name . ' is already exists', 'info');
        }
    }

    public function view(string $name): void
    {
        $location   = root_dir('/resources/views/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(strtolower(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR))) . DIRECTORY_SEPARATOR . strtolower($name) . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            if (file_put_contents($filepath, $this->getSchema('view'), LOCK_EX)) {
                $this->log('Done: ' . $name . ' View has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name . ' View', 'error');
            }
        } else {
            $this->log('View: ' . $name . ' is already exists', 'info');
        }
    }

    public function mail(string $name): void
    {
        $location   = root_dir('/resources/views/mail/');
        $div        = explode('/', $name);
        $name       = array_pop($div);
        $filepath   = $this->CheckDirLocation(strtolower(rtrim($location . $this->joinFolders($div), DIRECTORY_SEPARATOR))) . DIRECTORY_SEPARATOR . strtolower($name) . '.php';
        $filepath   = str_replace('/', DIRECTORY_SEPARATOR, $filepath);

        if (!file_exists($filepath)) {
            if (file_put_contents($filepath, $this->getSchema('mail'), LOCK_EX)) {
                $this->log('Done: ' . $name . ' Mail has been created', 'success');
                echo "$filepath\n";
            } else {
                $this->log('Error: Failed to Create ' . $name . ' Mail', 'error');
            }
        } else {
            $this->log('Mail: ' . $name . ' is already exists', 'info');
        }
    }

    protected function TotalExistedFiles(string $location): int
    {
        return count(array_filter(scandir($location), fn ($file) => pathinfo($file, PATHINFO_EXTENSION) == 'php'));
    }

    protected function CheckDirLocation($location): string
    {
        if (!is_dir($location)) {
            mkdir($location, 0777, true);
        }

        if (!is_writable($location)) {
            chmod($location, 0777);
        }

        return $location;
    }

    protected function joinFolders(array $div, $join = DIRECTORY_SEPARATOR): string
    {
        $div = array_map(fn ($dir) => ucfirst($dir), $div);
        return join($join, $div);
    }

    protected function getSchema(string $key): string
    {
        return (require __DIR__ . '/Schema/create.php')[$key];
    }

    protected function log($message, $type = null)
    {
        $message = $message . ' - [' . date('Y-m-d H:i:s') . ']';

        if (php_sapi_name() != 'cli') {
            echo $message . '<br/>';
            return;
        }

        echo match ($type) {
            'error' => "\n\033[31m$message \033[0m\n",
            'success' => "\n\033[32m$message \033[0m\n",
            'warning' => "\n\033[33m$message \033[0m\n",
            'info' => "\n\033[36m$message \033[0m\n",
            default => "\n$message\n"
        };
    }
}

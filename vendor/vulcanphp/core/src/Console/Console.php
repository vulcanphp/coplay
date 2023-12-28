<?php

namespace VulcanPhp\Core\Console;

class Console
{
    public string $command;

    public bool $found = false;

    public function run()
    {
        $args = $this->get_parsed_command();

        foreach (require __DIR__ . '/routes.php' as $command) {
            if (in_array($args['command'] ?? '', (array) $command['command'])) {
                // match with actions
                if (!isset($args['action']) || (isset($args['action']) && isset($command['action']) && in_array($args['action'], (array) $command['action']))) {
                    $this->resolve($command['callback'], $args);
                } elseif (isset($args['flags']) && isset($command['alias'])) {
                    // match with alias
                    foreach ((array) $command['alias'] as $alias) {
                        if (in_array($alias, $args['flags'])) {
                            $this->resolve($command['callback'], $args);
                        }
                    }
                }
            }
        }

        if (!$this->found) {
            echo sprintf(
                "\n\033[33mCommand: %s \033[0m\033[31m%s \033[0m\n\033[36m%s \033[0m\n",
                $this->command,
                'does not found',
                'run php artisan -h to get help'
            );
        }
    }

    public function resolve(array $callback, array $args = [])
    {
        $this->found = true;
        return call_user_func([new $callback[0], $callback[1]], $args);
    }

    public function get_parsed_command(): array
    {
        global $argv;

        unset($argv[0]);

        $this->command  = join(' ', $argv);
        $command        = array_shift($argv);

        return array_filter([
            'command' => strpos($command, ':') !== false ? substr($command, 0, strpos($command, ':')) : $command,
            'action'  => strpos($command, ':') !== false ? substr($command, strpos($command, ':') + 1) : null,
            'flags'   => $argv
        ]);
    }
}

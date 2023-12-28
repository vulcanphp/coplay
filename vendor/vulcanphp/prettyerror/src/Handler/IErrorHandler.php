<?php

namespace VulcanPhp\PrettyError\Handler;

abstract class IErrorHandler
{
    abstract public function handle($exception): void;

    protected function cleanBuffer(): self
    {
        if (isset(ob_get_status()['buffer_size']) && ob_get_status()['buffer_size'] > 0) {
            ob_end_clean();
            flush();
        }

        return $this;
    }

    protected function output(string $path, array $params = []): string
    {
        extract($params);
        ob_start();

        include $path;

        return ob_get_clean();
    }
}

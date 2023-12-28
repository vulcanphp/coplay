<?php

namespace VulcanPhp\PrettyError\Handler;

class CliErrorHandler extends IErrorHandler
{
    public function handle($exception): void
    {
        $trace = $exception->getTrace()[0] ?? [];
        if (!empty($trace)) {
            echo sprintf(
                "\n\033[36m%s \033[0m",
                (isset($trace['class']) ? $trace['class'] . '->' : '') . (isset($trace['function']) ? $trace['function'] . '()' : '') . (isset($trace['class']) || isset($trace['function']) ? ':' . $exception->getLine() : ''),
            );
        }

        echo sprintf(
            "\n\033[31m%s\033[0m\n\n\033[33mTrace:\033[0m\n%s\n",
            $exception->getMessage(),
            sprintf("    (1) %s:%s", $exception->getFile(), $exception->getLine())
        );

        foreach ($exception->getTrace() as $key => $trace) {
            if ($key == 0 || !isset($trace['file'])) {
                continue;
            }

            echo sprintf("    (%d) %s:%s\n", $key, $trace['file'], $trace['line']);
        }

        exit;
    }
}

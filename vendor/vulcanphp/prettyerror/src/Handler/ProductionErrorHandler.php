<?php

namespace VulcanPhp\PrettyError\Handler;

class ProductionErrorHandler extends IErrorHandler
{
    public function handle($exception): void
    {
        http_response_code(500);

        echo $this
            ->cleanBuffer()
            ->output(__DIR__ . '/../resources/abort.php', ['exception' => $exception]);

        exit;
    }
}

<?php

namespace VulcanPhp\PrettyError;

use ErrorException;
use VulcanPhp\PrettyError\Frame\PrettyFrame;
use VulcanPhp\PrettyError\Handler\CliErrorHandler;
use VulcanPhp\PrettyError\Handler\DevelopmentErrorHandler;
use VulcanPhp\PrettyError\Handler\ProductionErrorHandler;

class PrettyError
{
    public const ENV_DEVELOPMENT = 'development', ENV_PRODUCTION = 'production';
    protected string $env;

    public function __construct(string $env = self::ENV_DEVELOPMENT)
    {
        // setup pretty error configuration
        $this->env = $env;

        // enable display error
        if ($this->env === self::ENV_DEVELOPMENT) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
        }

        // set error handler method
        set_error_handler([$this, 'handleError'], E_ALL);

        // set exception handler
        set_exception_handler([$this, 'handleException']);

        // set fatal error handler
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public static function register(...$args): PrettyError
    {
        return new PrettyError(...$args);
    }

    public function handleError($level, $message, $file = null, $line = null)
    {
        $exception = new ErrorException($message, $level, $level, $file, $line);
        $this->handleException($exception);
    }

    public function handleException($exception)
    {
        if (php_sapi_name() == 'cli') {
            $handler = new CliErrorHandler();
        } else {
            if ($this->env === self::ENV_DEVELOPMENT) {
                $handler = new DevelopmentErrorHandler(new PrettyFrame(25));
            } else {
                $handler = new ProductionErrorHandler();
            }
        }

        $handler->handle($exception);
    }

    public function handleShutdown()
    {
        $error = error_get_last();

        if ($error && $this->isLevelFatal($error['type'])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    public function isLevelFatal($level)
    {
        $errors = E_ERROR;
        $errors |= E_PARSE;
        $errors |= E_CORE_ERROR;
        $errors |= E_CORE_WARNING;
        $errors |= E_COMPILE_ERROR;
        $errors |= E_COMPILE_WARNING;
        return ($level & $errors) > 0;
    }
}

<?php

use Spark\Container;
use Spark\Foundation\Application;
use Spark\Http\Middleware;

/**
 * This file is the entry point of the web application.
 *
 * It uses the tinymvc framework to create the application instance, register
 * service providers, middleware, and routes.
 */

/**
 * Create the application instance.
 *
 * @param string $path
 *   The directory path of the application.
 * @param array $env
 *   The environment settings of the application.
 */
return Application::make(path: dirname(__DIR__), env: require __DIR__ . '/../env.php')
    /**
     * Register service providers in the application container.
     *
     * @param Container $container
     *   The application container.
     *
     * @return void
     */
    ->withContainer(function (Container $container) {
        foreach (require __DIR__ . '/providers.php' as $provider) {
            $container->addServiceProvider(new $provider);
        }
    })

    /**
     * Register middleware in the application.
     *
     * @param Middleware $middleware
     *   The middleware service.
     *
     * @return void
     */
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->merge(require __DIR__ . '/middlewares.php');
    })

    /**
     * Register routes in the application.
     *
     * @param Router $router
     *   The router service.
     *
     * @return void
     */
    ->withRouter(function () {
        require __DIR__ . '/../routes/web.php'; // Load web routes
    });

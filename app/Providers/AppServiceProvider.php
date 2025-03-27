<?php

namespace Providers;

use Lib\TailwindHelper;
use Hyper\Container;

/**
 * This file contains the service providers for the web application.
 *
 * A service provider is a class that adds functionality to the
 * application. The service providers in this file are only loaded
 * when the application is running in web mode.
 */
class AppServiceProvider
{
    /**
     * Registers services in the application container.
     *
     * This method is called when the service provider is registered in the
     * application container. It is used to add services to the container
     * that can be used by the application.
     *
     * @param Container $container
     *   The application container.
     *
     * @return void
     */
    public function register(Container $container)
    {
        // Create the Tailwind helper service
        $container->singleton(TailwindHelper::class, function () {
            $colors = env('cms', [])['color'] ?? [];

            // Get the colors from the environment variable
            $accent = $colors['accent'] ?? 'amber';
            $primary = $colors['primary'] ?? 'gray';

            // Create the Tailwind helper service
            return new TailwindHelper([
                'colors' => ['accent' => $accent, 'primary' => $primary],
                'preloader' => [
                    'element' => '<div class="loader"></div>',
                    'style' => '.loader {width: 45px;aspect-ratio: 1;--c1: linear-gradient(90deg, #0000 calc(100%/3), var(--color-accent-400) 0 calc(2*100%/3), #0000 0);--c2: linear-gradient(0deg, #0000 calc(100%/3), var(--color-accent-400) 0 calc(2*100%/3), #0000 0);background: var(--c1), var(--c2), var(--c1), var(--c2);background-size: 300% 4px, 4px 300%;background-repeat: no-repeat;animation: l3 1s infinite linear;}@keyframes l3 {0% {background-position: 50% 0, 100% 100%, 0 100%, 0 0}25% {background-position: 0 0, 100% 50%, 0 100%, 0 0}50% {background-position: 0 0, 100% 0, 50% 100%, 0 0}75% {background-position: 0 0, 100% 0, 100% 100%, 0 50%}75.01% {background-position: 100% 0, 100% 0, 100% 100%, 0 50%}100% {background-position: 50% 0, 100% 0, 100% 100%, 0 100%}}#preloader {position: fixed;inset: 0;width: 100%;height: 100%;background: var(--color-primary-950);z-index: 999999;display: flex;align-items: center;justify-content: center;}'
                ],
            ]);
        });
    }

    /**
     * Boots the service provider.
     *
     * This method is called after all the service providers have been
     * registered. It is used to perform any necessary setup or bootstrapping
     * of the application.
     *
     * @return void
     */
    public function boot()
    {
        // i am bootstrapping services
    }
}
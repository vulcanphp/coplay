<?php

/**
 * Service Providers Configuration
 *
 * This file returns an array of service providers that are used
 * in the web application. Each service provider is a class that
 * adds functionality to the application. The service providers
 * listed here will be loaded when the application is running
 * in web mode.
 *
 * @return array
 *   An array of service provider class names.
 */

return [
    // Application service provider for registering and bootstrapping services
    \App\Providers\AppServiceProvider::class,
];

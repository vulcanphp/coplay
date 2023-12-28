<?php

use VulcanPhp\Core\Foundation\Application;
use VulcanPhp\FastCache\SiteCache;

// define root directory of the application
define('ROOT_DIR', __DIR__);

// register the composer auto loader
require_once __DIR__ . '/vendor/autoload.php';

// init site cache and serve from cache
$siteCache = SiteCache::setup([
    'tmp_dir'   => config('app.tmp_dir'),
    'except'    => ['/admin/*', '/embed/*', '/search/*'],
]);

// serve from cache if production mode
if (!is_dev()) {
    $siteCache->serve();
}

// create a new application instance for this http request
Application::create(__DIR__)
    ->registerKernel(App\Http\Kernels\AppKernel::class)
    ->run();

// stop the application execution process
exit;

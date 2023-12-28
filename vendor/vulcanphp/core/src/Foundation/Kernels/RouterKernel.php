<?php

namespace VulcanPhp\Core\Foundation\Kernels;

use App\Http\Middlewares\Cors;
use App\Http\Middlewares\WebMiddleware;
use VulcanPhp\Core\Foundation\Interfaces\IKernel;
use VulcanPhp\PhpRouter\Route;

class RouterKernel implements IKernel
{
    public function boot(): void
    {
        // register api routes
        Route::group(['middlewares' => [Cors::class], 'prefix' => 'api', 'name' => 'api.'], root_dir('/routes/api.php'));

        // register web routes
        Route::group(['middlewares' => [WebMiddleware::class]], root_dir('/routes/web.php'));
    }

    public function shutdown(): void
    {
    }
}

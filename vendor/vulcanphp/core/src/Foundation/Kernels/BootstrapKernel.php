<?php

namespace VulcanPhp\Core\Foundation\Kernels;

use App\Http\Middlewares\Csrf;
use VulcanPhp\SimpleDb\Model;
use VulcanPhp\FastCache\Cache;
use VulcanPhp\PhpRouter\Router;
use VulcanPhp\SimpleDb\Database;
use VulcanPhp\FileSystem\Storage;
use VulcanPhp\Core\Helpers\Session;
use VulcanPhp\PrettyError\PrettyError;
use VulcanPhp\Core\Foundation\Application;
use VulcanPhp\FastCache\Drivers\FastCacheDriver;
use VulcanPhp\Core\Foundation\Interfaces\IKernel;
use VulcanPhp\Translator\Drivers\GoogleTranslatorDriver;
use VulcanPhp\Translator\Manager\TranslatorFileManager;
use VulcanPhp\Translator\Translator;

class BootstrapKernel implements IKernel
{
    public function boot(): void
    {
        // set default timezone
        date_default_timezone_set(config('app.timezone'));

        // apply pretty error handler
        PrettyError::register(
            is_dev() ? PrettyError::ENV_DEVELOPMENT : PrettyError::ENV_PRODUCTION
        );

        // Session Start
        Session::start();

        // system cache init
        Cache::init(new FastCacheDriver([
            'path'      => config('app.tmp_dir'),
            'extension' => '.cache'
        ]));

        // set router postBack csrf middleware
        Application::$app
            ->getRouter()
            ->setMiddlewares([Csrf::class])
            ->setFallback(fn () => Application::$app->abort(404))
            ->setFilters(Router::FILTER['reflection_parameters'], function ($parameters) {
                $parameters[] = [
                    'parameter' => Model::class,
                    'callback' => function ($param, $params, $key, $value) {
                        if (isset($params[$param->getName()])) {
                            $condition = [$param->getName() => $params[$param->getName()]];
                        } else {
                            $pk = $param->getType()->getName()::primaryKey();
                            $condition = [(array_keys($params)[$key] ?? $pk) => $value ?? ($params[$pk] ?? null)];
                        }
                        return $param->getType()->getName()::findOrFail($condition);
                    }
                ];

                return $parameters;
            });

        // setup database
        Database::init(config('database'))
            ->getHookHandler()
            ->fallback('model_static', 'Cache', fn ($model) => cache('dbmodel_' . $model::tableName()))
            ->action('changed', fn ($table) => cache('dbmodel_' . $table)->flush())
            ->fallback('query', 'get', fn ($query) => $query->result())
            ->filter('result', fn ($data) => collect($data));

        // storage init
        Storage::init(config('app.storage_dir'));

        // initialize translator
        Translator::init(
            new GoogleTranslatorDriver(
                new TranslatorFileManager([
                    'convert'   => config('app.language'),
                    'local_dir' => config('app.language_dir'),
                ])
            )
        )->getDriver()->enableLazy();
    }

    public function shutdown(): void
    {
    }
}

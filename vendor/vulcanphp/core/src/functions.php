<?php

use VulcanPhp\Core\Helpers\Arr;
use VulcanPhp\PhpRouter\Router;
use VulcanPhp\Core\Helpers\Vite;
use VulcanPhp\Core\Helpers\Mixer;
use VulcanPhp\Core\Helpers\Bucket;
use VulcanPhp\Core\Helpers\Cookie;
use VulcanPhp\Core\Helpers\Session;
use VulcanPhp\InputMaster\Request;
use VulcanPhp\Core\Helpers\Collection;
use VulcanPhp\InputMaster\Response;
use VulcanPhp\Core\Foundation\Application;
use VulcanPhp\Core\Crypto\Password\Password;
use VulcanPhp\Core\Crypto\Password\Drivers\PasswordHash;

if (!function_exists('app')) {
    function app(): Application
    {
        return Application::$app;
    }
}

if (!function_exists('root_dir')) {
    function root_dir($path = '')
    {
        return str_replace(
            '/',
            DIRECTORY_SEPARATOR,
            defined('ROOT_DIR') ? ROOT_DIR : Application::$app->rootDir
        ) . (!empty($path)
            ? DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, ltrim($path, '/'))
            : ''
        );
    }
}

if (!function_exists('bucket')) {
    Bucket::init();

    function bucket(...$args)
    {
        return !empty($args)
            ? Bucket::$store->get(...$args)
            : Bucket::$store;
    }
}

if (!function_exists('abort')) {
    function abort(...$args)
    {
        return app()->abort(...$args);
    }
}

if (!function_exists('config')) {
    function config(string $map, $default = null, $reload = false)
    {
        $map   = explode('.', $map);
        $stage = array_shift($map);
        $key = 'config.' . $stage;

        if (!bucket()->has($key) || $reload) {
            $config = root_dir('/config/' . $stage . '.php');

            if ($reload) {
                bucket()->set(
                    $key,
                    eval(str_ireplace(
                        ['<?php', '?>'],
                        '',
                        (string) file_get_contents($config)
                    ))
                );
            } else {
                if (file_exists($config)) {
                    bucket()->set($key, require $config);
                } else {
                    bucket()->set($key, []);
                }
            }
        }

        return !empty($map)
            ? Arr::get(
                bucket($key),
                join('.', $map),
                $default
            )
            : bucket($key);
    }
}

if (!function_exists('session')) {
    function session(?string $name = null, $default = null)
    {
        if (func_num_args() == 0) {
            return Session::create();
        }

        return Session::get($name, $default);
    }
}

if (!function_exists('cookie')) {
    function cookie(?string $name = null, $default = null)
    {
        if ($name !== null) {
            return Cookie::get($name, $default);
        }

        return Cookie::class;
    }
}

if (!function_exists('home_url')) {
    function home_url($path = ''): string
    {
        return config('app.root_url', request()->rootUrl()->absoluteUrl())
            . (!empty($path) ? trim((string) $path, '/') : '');
    }
}

if (!function_exists('resource_url')) {
    function resource_url($path = ''): string
    {
        return is_url($path)
            ? $path
            : home_url(str_replace(
                [root_dir(), DIRECTORY_SEPARATOR],
                ['', '/'],
                resource_dir($path)
            ));
    }
}

if (!function_exists('resource_dir')) {
    function resource_dir($path = ''): string
    {
        return is_url($path)
            ? $path
            : str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                config('app.resource_dir')
            ) . (!empty($path)
                ? DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, trim((string) $path, '/'))
                : ''
            );
    }
}

if (!function_exists('collect')) {
    function collect(...$values): Collection
    {
        return new Collection(...$values);
    }
}

if (!function_exists('router')) {
    function router(): Router
    {
        return app()->getRouter();
    }
}

if (!function_exists('response')) {
    function response(): Response
    {
        return request()->getResponse();
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return router()->getRequest();
    }
}

if (!function_exists('url')) {
    function url(?string $route = null, ...$args)
    {
        if ($route !== null) {
            return router()->route($route, ...$args)->getUrl();
        }

        return request()->getUrl();
    }
}

if (!function_exists('input')) {
    function input(...$args)
    {
        $input = request()->inputHandler();

        if (func_num_args() == 0) {
            return $input;
        }

        return $input->value(...$args);
    }
}

if (!function_exists('redirect')) {
    function redirect(...$args): void
    {
        response()->redirect(...$args);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): ?string
    {
        return router()->getCsrfToken();
    }
}

if (!function_exists('csrf')) {
    function csrf(): string
    {
        return sprintf('<input type="hidden" name="_token" value="%s">', csrf_token());
    }
}

if (!function_exists('method')) {
    function method(string $method): string
    {
        return sprintf('<input type="hidden" name="_method" value="%s">', strtolower($method));
    }
}

if (!function_exists('password')) {
    function password(...$args)
    {
        $password = Password::create(new PasswordHash);

        return match (func_num_args()) {
            2 => $password->verify(...$args),
            1 => $password->generate(...$args),
            default => $password
        };
    }
}

if (!function_exists('dump')) {
    function dump($var): void
    {
        if (is_bool($var)) {
            $var = 'bool(' . ($var ? 'true' : 'false') . ')';
        }

        if (php_sapi_name() === 'cli') {
            print_r($var);
        } else {
            highlight_string("<?php\n" . var_export($var, true) . "\n\n");
        }
    }
}

if (!function_exists('dd')) {
    function dd($ver): bool
    {
        dump($ver);
        exit;
    }
}

if (!function_exists('is_dev')) {
    function is_dev(): bool
    {
        return boolval(config('app.development')) === true;
    }
}

if (!function_exists('is_url')) {
    function is_url(?string $text): bool
    {
        if (filter_var($text, FILTER_VALIDATE_URL)) {
            return true;
        }

        return preg_match('/(http|https):\/\/[a-z0-9]+[a-z0-9_\/]*/', $text ?? '') === 1;
    }
}

if (!function_exists('like_match')) {
    function like_match($pattern, $subject): bool
    {
        $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));

        return (bool) preg_match("/^{$pattern}$/i", $subject);
    }
}

if (!function_exists('encode_string')) {
    function encode_string($value): ?string
    {
        if (in_array(gettype($value), ['array'])) {
            $value = json_encode($value, JSON_UNESCAPED_UNICODE);
        } elseif (in_array(gettype($value), ['object'])) {
            $value = serialize($value);
        }

        return $value;
    }
}

if (!function_exists('decode_string')) {
    function decode_string($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $decoded = json_decode(
            preg_replace(['/,+/', '/\[,/'], [',', '['], $value),
            true,
            JSON_UNESCAPED_UNICODE
        );

        if (!is_array($decoded) && preg_match("#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $value)) {
            $decoded = mb_unserialize($value);
        }

        if (in_array(gettype($decoded), ['object', 'array'])) {
            $value = $decoded;
        }

        return $value;
    }
}

if (!function_exists('mb_unserialize')) {
    function mb_unserialize($string)
    {
        $string2 = preg_replace_callback(
            '!s:(\d+):"(.*?)";!s',
            function ($m) {
                $len    = strlen($m[2]);
                $result = "s:$len:\"{$m[2]}\";";
                return $result;
            },
            $string
        );
        return unserialize($string2);
    }
}

if (!function_exists('__is_int')) {
    function __is_int($value): bool
    {
        return is_int($value)
            || (isset($value)
                && is_string($value)
                && (intval($value) == $value)
            );
    }
}

if (!function_exists('mixer')) {
    function mixer()
    {
        return Mixer::create();
    }
}

if (!function_exists('__lang')) {
    function __lang(): string
    {
        return translator_manager()->getLanguage();
    }
}

if (!function_exists('vite')) {
    function vite(...$args)
    {
        return Vite::create(...$args);
    }
}

if (!function_exists('vite_view')) {
    function vite_view(string $template = 'index', array $params = [])
    {
        return view()
            ->getDriver()
            ->getEngine()
            ->resourceDir(config('app.vite_dir'))
            ->template($template)
            ->render($params);
    }
}

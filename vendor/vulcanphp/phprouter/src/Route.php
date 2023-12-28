<?php

namespace VulcanPhp\PhpRouter;

use VulcanPhp\PhpRouter\Routing\Exceptions\RouterException;
use VulcanPhp\PhpRouter\Routing\GroupedRoute;
use VulcanPhp\PhpRouter\Routing\ResourceRoute;
use VulcanPhp\PhpRouter\Routing\GeneralRoute;

class Route
{
    public static function get(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['get']]);
    }

    public static function post(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['post']]);
    }

    public static function put(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['put']]);
    }

    public static function patch(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['patch']]);
    }

    public static function delete(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['delete']]);
    }

    public static function options(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['options']]);
    }

    public static function match(array $methods, string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => $methods]);
    }

    public static function form(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['get', 'post']]);
    }

    public static function any(string $url, $callback): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => $callback, 'methods' => ['get', 'post', 'put', 'patch', 'delete', 'options']]);
    }

    public static function regex(string $regex, $methods, $callback): GeneralRoute
    {
        return new GeneralRoute($regex, ['callback' => $callback, 'methods' => $methods, 'regex' => $regex]);
    }

    public static function resource(...$args): ResourceRoute
    {
        return new ResourceRoute(...$args);
    }

    public static function resources(array $resources): void
    {
        foreach ($resources as $path => $callback) {
            new ResourceRoute($path, $callback);
        }
    }

    public static function redirect(string $here, string $there, int $status = 0): GeneralRoute
    {
        return new GeneralRoute($here, ['callback' => function () use ($there, $status) {
            header("Location: $there", true, $status);
            exit;
        }, 'methods'  => ['get']]);
    }

    public static function view(string $url, string $template, array $args = []): GeneralRoute
    {
        return new GeneralRoute($url, ['callback' => function () use ($template, $args) {
            if (!function_exists('view')) {
                throw new RouterException('Function view(): does not exists');
            }

            return view($template, $args);
        }, 'methods'  => ['get']]);
    }

    public static function group(array $settings = [], $callback = null): GroupedRoute
    {
        return new GroupedRoute($settings, $callback);
    }
}

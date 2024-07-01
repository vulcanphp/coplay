<?php

use VulcanPhp\SweetView\Drivers\FireDriver;
use VulcanPhp\SweetView\Drivers\HtmlDriver;
use VulcanPhp\SweetView\SweetView;

if (!function_exists('sweet_view_root_dir')) {
    function sweet_view_root_dir($path = '')
    {
        return (defined('ROOT_DIR') ? ROOT_DIR : (function_exists('root_dir') ? root_dir() : getcwd())) . $path;
    }
}

if (!function_exists('minify_text')) {
    function minify_text($text = '')
    {
        return preg_replace(['/^ {2,}/m', '/^\t{2,}/m', '~[\r\n]+~'], '',  $text);
    }
}

if (!function_exists('view')) {
    function view(...$args)
    {
        $config = (array) function_exists('config') ? config('view') : [];
        $driver = isset($config['driver']) ?
            new $config['driver']($config['extension'] ?? null, $config['base_dir'] ?? null) :
            new HtmlDriver($config['extension'] ?? null, $config['base_dir'] ?? null);
        $engine = SweetView::create($driver);
        return func_num_args() == 0 ? $engine : $engine->render(...$args);
    }
}

if (!function_exists('fire_view')) {
    function fire_view(...$args)
    {
        $engine = SweetView::create(new FireDriver);
        return func_num_args() == 0 ? $engine : $engine->render(...$args);
    }
}


if (!function_exists('fire_link')) {
    function fire_link(): string
    {
        return '<script defer src="' . fire_script_url() . '"></script>';
    }
}

if (!function_exists('fire_script_url')) {
    function fire_script_url(): string
    {
        return '/vendor/vulcanphp/sweetview/src/Engine/Fire/fire.js';
    }
}

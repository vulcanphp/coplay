<?php

use VulcanPhp\SweetView\SweetView;

if (!function_exists('sweet_view_root_dir')) {
    function sweet_view_root_dir($path = '')
    {
        return (defined('ROOT_DIR') ? ROOT_DIR : (function_exists('root_dir') ? root_dir() : getcwd())) . $path;
    }
}

if (!function_exists('view')) {
    function view(...$args)
    {
        return func_num_args() == 0 ? SweetView::create() : SweetView::create()->render(...$args);
    }
}

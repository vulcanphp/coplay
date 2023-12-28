<?php

use VulcanPhp\FileSystem\Handler\FileHandler;
use VulcanPhp\FileSystem\Handler\FolderHandler;
use VulcanPhp\FileSystem\Handler\ImageHandler;
use VulcanPhp\FileSystem\Interfaces\IStorageHandler;
use VulcanPhp\FileSystem\Storage;

if (!function_exists('storage_init')) {
    function storage_init(...$args): Storage
    {
        return Storage::init(...$args);
    }
}

if (!function_exists('storage')) {
    function storage(): IStorageHandler
    {
        return Storage::$instance->getHandler();
    }
}

if (!function_exists('storage_dir')) {
    function storage_dir($path = ''): string
    {
        return storage()->getPath()
            . (!empty($path) ? DIRECTORY_SEPARATOR . str_replace(['//', '/'], DIRECTORY_SEPARATOR, trim($path, '/')) : '');
    }
}

if (!function_exists('storage_url')) {
    function storage_url($path = ''): string
    {
        return storage()->getUrl()
            . (!empty($path) ? '/' . str_replace(DIRECTORY_SEPARATOR, '/', trim($path, '/')) : '');
    }
}

if (!function_exists('file_handler')) {
    function file_handler($path): FileHandler
    {
        return new FileHandler($path);
    }
}

if (!function_exists('folder_handler')) {
    function folder_handler($path): FolderHandler
    {
        return new FolderHandler($path);
    }
}

if (!function_exists('image_handler')) {
    function image_handler($path): ImageHandler
    {
        return new ImageHandler($path);
    }
}

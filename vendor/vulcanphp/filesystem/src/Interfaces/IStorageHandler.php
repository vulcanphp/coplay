<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IStorageHandler
{
    public function getConfig(string $key, $default = null);

    public function setConfig(string $key, $value): void;

    public function download(string $fileName): void;

    public function downloadZip($files, string $zipName): void;

    public function uploadFile(array $file, string $mode = 'keep'): string;

    public function upload(string $index, ...$args): array;

    public function uploadFromUrl(string $url, bool $override = false): string;
}

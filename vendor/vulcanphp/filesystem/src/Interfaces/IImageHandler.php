<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IImageHandler
{
    public function compress(int $quality = 75, $destination = null): bool;

    public function resize(int $img_width, int $img_height, ?string $destination = null): bool;

    public function bulkResize(array $sizes): array;

    public function rotate(float $degrees): bool;
}

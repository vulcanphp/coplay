<?php

namespace VulcanPhp\FileSystem;

use VulcanPhp\FileSystem\Handler\ImageHandler;
use VulcanPhp\FileSystem\Interfaces\IImage;
use VulcanPhp\FileSystem\Includes\BaseHandler;
use VulcanPhp\FileSystem\Interfaces\IImageHandler;

class Image extends BaseHandler implements IImage
{
    public function __construct(...$args)
    {
        $this->setHandler(new ImageHandler(...$args));
    }

    public function setHandler(IImageHandler $Handler): void
    {
        $this->Handler = $Handler;
    }

    public static function sizeText($image, int $width, int $height)
    {
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        return str_ireplace('.' . $ext, '-' . $width . 'x' . $height . '.' . $ext, $image);
    }
}

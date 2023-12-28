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
}

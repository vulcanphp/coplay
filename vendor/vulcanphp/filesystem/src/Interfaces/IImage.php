<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IImage
{
    public function setHandler(IImageHandler $Handler): void;
}

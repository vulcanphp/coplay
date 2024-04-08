<?php

namespace VulcanPhp\FileSystem;

use VulcanPhp\FileSystem\Handler\FileHandler;
use VulcanPhp\FileSystem\Includes\BaseHandler;
use VulcanPhp\FileSystem\Interfaces\IFile;
use VulcanPhp\FileSystem\Interfaces\IFileHandler;

class File extends BaseHandler implements IFile
{
    public function __construct(...$args)
    {
        $this->setHandler(new FileHandler(...$args));
    }

    public function setHandler(IFileHandler $Handler): void
    {
        $this->Handler = $Handler;
    }
}

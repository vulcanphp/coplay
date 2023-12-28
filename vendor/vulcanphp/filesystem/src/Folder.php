<?php

namespace VulcanPhp\FileSystem;

use VulcanPhp\FileSystem\Handler\FolderHandler;
use VulcanPhp\FileSystem\Includes\BaseHandler;
use VulcanPhp\FileSystem\Interfaces\IFolder;
use VulcanPhp\FileSystem\Interfaces\IFolderHandler;

class Folder extends BaseHandler implements IFolder
{
    public function __construct(...$args)
    {
        $this->setHandler(new FolderHandler(...$args));
    }

    public function setHandler(IFolderHandler $Handler): void
    {
        $this->Handler = $Handler;
    }
}

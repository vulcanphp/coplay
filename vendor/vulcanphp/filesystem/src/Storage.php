<?php

namespace VulcanPhp\FileSystem;

use VulcanPhp\FileSystem\Handler\StorageHandler;
use VulcanPhp\FileSystem\Includes\BaseHandler;
use VulcanPhp\FileSystem\Interfaces\IStorage;
use VulcanPhp\FileSystem\Interfaces\IStorageHandler;

class Storage extends BaseHandler implements IStorage
{
    public function __construct(...$args)
    {
        $this->setHandler(new StorageHandler(...$args));
    }

    public function setHandler(IStorageHandler $Handler): void
    {
        $this->Handler = $Handler;
    }
}

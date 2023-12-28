<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IStorage
{
    public function setHandler(IStorageHandler $Handler): void;
}

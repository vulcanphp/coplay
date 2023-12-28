<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IFile
{
    public function setHandler(IFileHandler $Handler): void;
}

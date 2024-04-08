<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IFolder
{
    public function setHandler(IFolderHandler $Handler): void;
}

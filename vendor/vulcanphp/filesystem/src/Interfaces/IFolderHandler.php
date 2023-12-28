<?php

namespace VulcanPhp\FileSystem\Interfaces;

use VulcanPhp\FileSystem\Handler\FileHandler;

interface IFolderHandler
{
    public function setPath(string $folderPath): void;

    public function getPath(): string;

    public function enter(string $dirname): self;

    public function back(): self;

    public function getFile(string $filename): FileHandler;

    public function is(): bool;

    public function writable(): bool;

    public function readable(): bool;

    public function scan(): array;

    public function create(int $per = 0777, bool $rec = false): bool;

    public function chmod(int $permission = 0777): void;

    public function remove(): bool;
}

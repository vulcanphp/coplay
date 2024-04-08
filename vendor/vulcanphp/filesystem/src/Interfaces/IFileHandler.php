<?php

namespace VulcanPhp\FileSystem\Interfaces;

interface IFileHandler
{
    public function setPath(string $filePath): void;

    public function getPath(): string;

    public function getContent();

    public function putContent(string $contents): bool;

    public function move(string $path): bool;

    public function rename(string $path): bool;

    public function copy(string $path): bool;

    public function remove(): bool;

    public function getMimeType(): ?string;

    public function getExt(): ?string;

    public function getName(): string;

    public function getDirName(): string;

    public function getSize();

    public function getMtime();

    public function exists(): bool;

    public function is(): bool;
}

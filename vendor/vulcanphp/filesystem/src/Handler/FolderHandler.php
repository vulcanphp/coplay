<?php

namespace VulcanPhp\FileSystem\Handler;

use VulcanPhp\FileSystem\Exceptions\FolderException;
use VulcanPhp\FileSystem\Includes\FileHelper;
use VulcanPhp\FileSystem\Interfaces\IFolderHandler;

class FolderHandler implements IFolderHandler
{
    use FileHelper;

    protected ?string $folderPath;

    public function __construct(?string $folderPath = null)
    {
        if ($folderPath !== null) {
            $this->setPath($folderPath);
        }
    }

    public function setPath(string $folderPath): void
    {
        $this->folderPath = str_replace(['//', '/'], DIRECTORY_SEPARATOR, $folderPath);
    }

    public function getPath(): string
    {
        if (!isset($this->folderPath)) {
            throw new FolderException("Folder Path does not specified");
        }

        return $this->folderPath;
    }

    public function enter(string $dirname): self
    {
        $this->setPath($this->getPath() . DIRECTORY_SEPARATOR . trim($dirname, '/'));

        return $this;
    }

    public function back(): self
    {
        $this->setPath(substr($this->getPath(), 0, strrpos($this->getPath(), DIRECTORY_SEPARATOR)));

        return $this;
    }

    public function is(): bool
    {
        return is_dir($this->getPath());
    }

    public function writable(): bool
    {
        return is_writable($this->getPath());
    }

    public function readable(): bool
    {
        return is_readable($this->getPath());
    }

    public function getFile(string $filename): FileHandler
    {
        return new FileHandler($this->getPath() . DIRECTORY_SEPARATOR . trim($filename, '/'));
    }

    public function scan(): array
    {
        if (!$this->is()) {
            throw new FolderException(sprintf("Folder [%s] must be a directory", $this->getPath()));
        }

        $result = [];

        foreach (scandir($this->getPath()) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            $fullPath = $this->getPath() . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $result[] = new FolderHandler($fullPath);
            } elseif (is_file($fullPath)) {
                $result[] = new FileHandler($fullPath);
            }
        }

        return $result;
    }

    public function create(int $per = 0777, bool $rec = false): bool
    {
        if (!$this->is()) {
            $created = mkdir($this->getPath(), $per, $rec);

            if ($created === false) {
                throw new FolderException(sprintf("Unable to create new folder [%s].", $this->getPath()));
            }

            return $created;
        }

        return false;
    }

    public function chmod(int $permission = 0777): void
    {
        if (chmod($this->getPath(), $permission) === false) {
            throw new FolderException(sprintf('Folder [%s] must be readable and writeable', $this->getPath()));
        }
    }

    public function check(): void
    {
        $this->create(0777, true);

        if (!$this->writable() || !$this->readable()) {
            $this->chmod(0777);
        }
    }

    // remove current entire folder and contents
    public function remove(): bool
    {
        if (!is_dir($this->getPath())) {
            throw new FolderException(sprintf("Folder [%s] must be a directory", $this->getPath()));
        }

        foreach ($this->scan() as $result) {
            if ($result instanceof FolderHandler) {
                $result->remove();
            } elseif ($result instanceof FileHandler) {
                $result->remove();
            }
        }

        return rmdir($this->getPath());
    }

    // delete only file under the current folder
    public function delete($files): void
    {
        foreach ((array) $files as $file) {
            $resource = $this->getPath() . DIRECTORY_SEPARATOR . trim($file, '/');
            if (is_file($resource)) {
                unlink($resource);
            } elseif (is_dir($resource)) {
                (new FolderHandler($resource))->remove($resource);
            }
        }
    }
}

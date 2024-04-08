<?php

namespace VulcanPhp\FileSystem\Handler;

use VulcanPhp\FileSystem\Exceptions\FileException;
use VulcanPhp\FileSystem\Includes\FileHelper;
use VulcanPhp\FileSystem\Interfaces\IFileHandler;

class FileHandler implements IFileHandler
{
    use FileHelper;

    protected ?string $filePath;

    public function __construct(?string $filePath = null)
    {
        if ($filePath !== null) {
            $this->setPath($filePath);
        }
    }

    public function setPath(string $filePath): void
    {
		$this->filePath = filter_var($filePath, FILTER_VALIDATE_URL) ? $filePath : str_replace(['//', '/'], DIRECTORY_SEPARATOR, $filePath);
    }

    public function getPath(): string
    {
        if (!isset($this->filePath)) {
            throw new FileException("Filepath does not specified");
        }

        return $this->filePath;
    }

    public function is(): bool
    {
        return is_file($this->getPath());
    }

    public function exists(): bool
    {
        return file_exists($this->getPath());
    }

    public function getMtime()
    {
        return filemtime($this->getPath());
    }

    public function getSize()
    {
        return filesize($this->getPath());
    }

    public function getName(): string
    {
        return basename($this->getPath());
    }

    public function getDirName(): string
    {
        return dirname($this->getPath());
    }

    public function getExt(): ?string
    {
        if ($this->is()) {
            return pathinfo($this->getPath(), PATHINFO_EXTENSION);
        } elseif (filter_var($this->getPath(), FILTER_VALIDATE_URL)) {
            $ext = explode('.', $this->getPath());
            return end($ext);
        }

        return null;
    }

    public function getMimeType(): ?string
    {
        if ($this->is()) {
            if (function_exists('mime_content_type')) {
                return mime_content_type($this->getPath());
            } elseif (function_exists('finfo_open')) {
                $finfo    = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $this->getPath());

                finfo_close($finfo);

                return $mimetype;
            }

            return 'application/octet-stream';
        }

        return null;
    }

    public function getBytes(): string
    {
        $size = $this->getSize();

        if ($size > 0) {
            $base   = log($size) / log(1024);
            $suffix = array(" B", " KB", " MB", " GB", " TB");

            return round(pow(1024, $base - floor($base)), 1) . $suffix[floor($base)];
        }

        return 0;
    }

    public function remove(): bool
    {
        if (!$this->exists()) {
            throw new FileException(sprintf("Unable to locate file [%s].", $this->getPath()));
        }

        $deleted = unlink($this->getPath());

        if ($deleted === false) {
            throw new FileException(sprintf("Unable to remove file [%s].", $this->getPath()));
        }

        return $deleted;
    }

    public function getContent()
    {
        if (!$this->exists() && !filter_var($this->getPath(), FILTER_VALIDATE_URL)) {
            throw new FileException(sprintf('File [%s] Dose\'t exists.', $this->getPath()));
        }

        return file_get_contents($this->getPath());
    }

    public function putContent(string $contents): bool
    {
        $saved = file_put_contents($this->getPath(), $contents, LOCK_EX);

        if ($saved === false) {
            throw new FileException(sprintf("Unable to save the file [%s].", $this->getPath()));
        }

        return $saved;
    }

    public function copy(string $path): bool
    {
        $saved = copy($this->getPath(), $path);

        if ($saved === false) {
            throw new FileException(sprintf("Unable to copy the file to [%s].", $path));
        }

        return $saved;
    }

    public function rename(string $path): bool
    {
        $saved = rename($this->getPath(), $path);

        if ($saved === false) {
            throw new FileException(sprintf("Unable to rename the file to [%s].", $path));
        }

        return $saved;
    }

    public function move(string $path): bool
    {
        return $this->copy($path) && $this->remove();
    }

    public function open(string $mode = 'w')
    {
        return fopen($this->getPath(), $mode);
    }

    public function readFile()
    {
        return readfile($this->getPath());
    }

    public function read($resource, ...$args)
    {
        return fread($resource, ...$args);
    }

    public function close($resource)
    {
        return fclose($resource);
    }

    public function end($resource)
    {
        return feof($resource);
    }
}

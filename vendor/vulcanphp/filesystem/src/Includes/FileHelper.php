<?php

namespace VulcanPhp\FileSystem\Includes;

use VulcanPhp\FileSystem\Exceptions\FileException;

trait FileHelper
{
    public function getUrl(): string
    {
        return filter_var($this->getPath(), FILTER_VALIDATE_URL)
            ? $this->getPath()
            : sprintf(
                "%s://%s/%s",
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http',
                $_SERVER['HTTP_HOST'],
                trim(str_ireplace([$_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR], ['', '/'], $this->getPath()), '/')
            );
    }

    public function getType(): ?string
    {
        return filetype($this->getPath());
    }

    public function __call($name, $arguments)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], ...$arguments);
        }

        throw new FileException('Call to Undefined Method: ' . $name . ', on (' . static::class . ')');
    }
}

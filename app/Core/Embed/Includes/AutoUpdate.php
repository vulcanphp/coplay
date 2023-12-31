<?php

namespace App\Core\Embed\Includes;

use App\Core\Embed\Interfaces\IAutoUpdate;

class AutoUpdate implements IAutoUpdate
{
    public function __construct(protected string $mode = 'monthly')
    {
    }

    public function getSchemaLocation(): string
    {
        return dirname(__DIR__) . '/schema.json';
    }

    public function check(bool $force = false): void
    {
        if ($force || !file_exists($this->getSchemaLocation()) || $this->isExpired()) {
            file_put_contents(
                $this->getSchemaLocation(),
                file_get_contents('https://raw.githubusercontent.com/vulcanphp/coplay/main/app/Core/Embed/schema.json')
            );
        }
    }

    public function isExpired(): bool
    {
        $duration = [
            'daily'     => '-1 day',
            'weekly'    => '-1 week',
            'monthly'   => '-1 month',
        ][strtolower($this->mode)] ?? '-1 year';

        return filemtime($this->getSchemaLocation()) < strtotime($duration);
    }
}

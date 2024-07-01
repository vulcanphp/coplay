<?php

namespace VulcanPhp\SweetView\Drivers;

use VulcanPhp\SweetView\Engine\Html\Html;
use VulcanPhp\SweetView\Interfaces\IEngine;
use VulcanPhp\SweetView\Interfaces\IViewDriver;

class HtmlDriver implements IViewDriver
{
    protected const EXTENSION = '.php', BASE_DIR = 'resources/views';
    protected IEngine $engine;

    public function __construct(?string $extension = null, ?string $baseDir = null)
    {
        $this->engine = new Html;
        $this->engine
            ->extension($extension ?? self::EXTENSION)
            ->resourceDir($baseDir ?? self::BASE_DIR);
    }

    public function dispatchView(string $template, array $parameters = []): string
    {
        return $this->getEngine()
            ->template($template)
            ->render($parameters);
    }

    public function getEngine(): Html
    {
        return $this->engine;
    }
}

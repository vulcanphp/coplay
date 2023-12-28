<?php

namespace VulcanPhp\SweetView\Drivers;

use VulcanPhp\SweetView\Engine\Html\Html;
use VulcanPhp\SweetView\Interfaces\IEngine;
use VulcanPhp\SweetView\Interfaces\IViewDriver;

class HtmlDriver implements IViewDriver
{
    protected const EXTENSION = '.php', BASE_DIR = 'resources/views';
    protected IEngine $engine;

    public function __construct()
    {
        $this->engine = new Html;
        $this->engine->resourceDir(self::BASE_DIR)->extension(self::EXTENSION);
    }

    public function dispatchView(string $template, array $paramiters = []): string
    {
        return $this->getEngine()->template($template)->render($paramiters);
    }

    public function getEngine(): IEngine
    {
        return $this->engine;
    }
}

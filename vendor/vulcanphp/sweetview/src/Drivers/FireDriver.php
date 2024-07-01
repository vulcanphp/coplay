<?php

namespace VulcanPhp\SweetView\Drivers;

use VulcanPhp\SweetView\Engine\Fire\FireView;
use VulcanPhp\SweetView\Interfaces\IEngine;
use VulcanPhp\SweetView\Interfaces\IViewDriver;

class FireDriver implements IViewDriver
{
    protected const EXTENSION = '.fire.php', BASE_DIR = 'resources/views';
    protected IEngine $engine;

    public function __construct(?string $extension = null, ?string $baseDir = null)
    {
        $this->engine = new FireView(
            $extension ?? self::BASE_DIR,
            $baseDir ?? self::EXTENSION
        );
    }

    public function dispatchView(string $template, array $parameters = []): string
    {
        return $this->getEngine()
            ->template($template)
            ->render($parameters);
    }

    public function getEngine(): FireView
    {
        return $this->engine;
    }
}

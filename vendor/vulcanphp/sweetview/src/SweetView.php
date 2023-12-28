<?php

namespace VulcanPhp\SweetView;

use VulcanPhp\SweetView\Drivers\HtmlDriver;
use VulcanPhp\SweetView\Interfaces\IView;
use VulcanPhp\SweetView\Interfaces\IViewDriver;

class SweetView implements IView
{
    protected IViewDriver $Driver;

    public function __construct(?IViewDriver $Driver = null)
    {
        $this->setDriver($Driver ?: new HtmlDriver);
    }

    public static function create(...$args): SweetView
    {
        return new SweetView(...$args);
    }

    public function setDriver(IViewDriver $Driver): self
    {
        $this->Driver = $Driver;
        return $this;
    }

    public function render(...$args): string
    {
        return $this->getDriver()->dispatchView(...$args);
    }

    public function getDriver(): IViewDriver
    {
        return $this->Driver;
    }
}

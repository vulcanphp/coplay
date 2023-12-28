<?php

namespace VulcanPhp\SweetView\Interfaces;

interface IViewDriver
{
    public function getEngine(): IEngine;

    public function dispatchView(string $template, array $paramiters = []): string;
}

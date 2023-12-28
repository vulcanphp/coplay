<?php

namespace VulcanPhp\PrettyError\Frame;

interface IFrame
{
    public function render(string $file, int $line_no): string;
}

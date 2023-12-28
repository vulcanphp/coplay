<?php

namespace VulcanPhp\Translator\Interfaces;

interface IGoogleTokenProvider
{
    public function generateToken(string $text): string;
}

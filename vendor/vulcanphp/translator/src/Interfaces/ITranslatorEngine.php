<?php

namespace VulcanPhp\Translator\Interfaces;

interface ITranslatorEngine
{
    public function translateFromString(string $text, string $source, string $convert): string;

    public function translateFromArray(array $texts, string $source, string $convert): array;
}

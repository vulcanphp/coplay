<?php

namespace VulcanPhp\Translator\Interfaces;

interface ITranslatorManager
{
    public function getLanguage(): string;

    public function getSourceLanguage(): string;

    public function hasLocal(string $key): bool;

    public function getLocal(string $key): ?string;

    public function updateLocal(string $key, string $data): void;

    public function setLocal(array $LocalData): void;

    public function saveLocal(): void;
}

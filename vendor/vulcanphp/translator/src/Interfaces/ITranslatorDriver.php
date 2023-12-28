<?php

namespace VulcanPhp\Translator\Interfaces;

interface ITranslatorDriver
{
    public function setEngine(ITranslatorEngine $Engine): void;

    public function getEngine(): ITranslatorEngine;

    public function setManager(ITranslatorManager $Manager): void;

    public function getManager(): ITranslatorManager;

    public function translate($text = ''): ?string;
}

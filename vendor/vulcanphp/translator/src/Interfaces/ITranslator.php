<?php

namespace VulcanPhp\Translator\Interfaces;

interface ITranslator
{
    public function setDriver(ITranslatorDriver $Driver): ITranslator;

    public function getDriver(): ITranslatorDriver;
}

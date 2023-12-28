<?php

namespace VulcanPhp\Translator;

use VulcanPhp\Translator\Drivers\GoogleTranslatorDriver;
use VulcanPhp\Translator\Interfaces\ITranslator;
use VulcanPhp\Translator\Interfaces\ITranslatorDriver;
use VulcanPhp\Translator\Manager\TranslatorFileManager;

class Translator implements ITranslator
{
    public static Translator $instance;

    protected ITranslatorDriver $Driver;

    public function __construct(?ITranslatorDriver $Driver = null)
    {
        $this->setDriver(
            $Driver ?: new GoogleTranslatorDriver(
                new TranslatorFileManager()
            )
        );
    }

    public static function init(...$args): Translator
    {
        return self::$instance = new Translator(...$args);
    }

    public static function create(...$args): Translator
    {
        return new Translator(...$args);
    }

    public function setDriver(ITranslatorDriver $Driver): Translator
    {
        $this->Driver = $Driver;
        return $this;
    }

    public function getDriver(): ITranslatorDriver
    {
        return $this->Driver;
    }
}

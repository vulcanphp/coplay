<?php

use VulcanPhp\Translator\Drivers\GoogleTranslatorDriver;
use VulcanPhp\Translator\Interfaces\ITranslatorDriver;
use VulcanPhp\Translator\Interfaces\ITranslatorEngine;
use VulcanPhp\Translator\Interfaces\ITranslatorManager;
use VulcanPhp\Translator\Manager\TranslatorFileManager;
use VulcanPhp\Translator\Translator;

if (!function_exists('init_translator')) {
    function init_translator(array $setup)
    {
        return Translator::init(
            new GoogleTranslatorDriver(
                new TranslatorFileManager($setup)
            )
        );
    }
}

if (!function_exists('translator_driver')) {
    function translator_driver(): ITranslatorDriver
    {
        return Translator::$instance->getDriver();
    }
}

if (!function_exists('translator_engine')) {
    function translator_engine(): ITranslatorEngine
    {
        return Translator::$instance->getDriver()->getEngine();
    }
}

if (!function_exists('translator_manager')) {
    function translator_manager(): ITranslatorManager
    {
        return Translator::$instance->getDriver()->getManager();
    }
}

if (!function_exists('enable_lazy_translator')) {
    function enable_lazy_translator(): void
    {
        translator_driver()->enableLazy();
    }
}

if (!function_exists('translate')) {
    function translate($text = ''): ?string
    {
        return translator_driver()->translate($text);
    }
}

if (!function_exists('translate_from_string')) {
    function translate_from_string(...$args): string
    {
        return translator_engine()->translateFromString(...$args);
    }
}

if (!function_exists('translate_from_array')) {
    function translate_from_array(...$args): array
    {
        return translator_engine()->translateFromArray(...$args);
    }
}

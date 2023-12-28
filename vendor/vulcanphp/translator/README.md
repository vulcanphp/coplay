# Simple Translator
Simple Translator is for PHP Application to translate texts with many local languages

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install this PHP Simple Translator

```bash
$ composer require vulcanphp/translator
```

## Get Started
After Installing this PHP Simple Translator, initialize it in your application.

```php
<?php
// index.php

use VulcanPhp\Translator\Drivers\GoogleTranslatorDriver;
use VulcanPhp\Translator\Manager\TranslatorFileManager;
use VulcanPhp\Translator\Translator;

require_once __DIR__ . '/vendor/autoload.php';

// initialize translator advanced
Translator::init(
    // user google translator driver to translate text
    new GoogleTranslatorDriver(
        // use filesystem manager to store translated text
        // local filesystem configuration
        new TranslatorFileManager([
            // source language from
            'source' => 'en',

            // target/convert language to
            'convert' => 'bn',

            // suffix for translated file to make it slim, EX: 'en-[admin].json'
            // [admin] is suffix for this certain language file
            'suffix' => null,

            // local direcotry to store translated files
            'local_dir' => __DIR__ . '/translate',
        ])
    )
);
// or, simply use helper function
init_translator([
    // local filesystem configuration for manager
    'convert' => 'bn',
    // ...
]);

// IMPORTANT:: there is a cool feature for google translator
// this is lazyLoad, if you use about hundred+ times to translate texts
// from google, then it will make the process slower. to make it faster,
// enable lazy translator just after initialize the translate instance in index.php
// this is optional but HIGHLY RECOMMENDED
enable_lazy_translator();

// Now, simply call translate() function allover in your application
var_dump(translate('Hello World!'));

// ...
```
### How it's Work?
This Translator can use different external sources to translate texts and store it somewhere in application.
as a source this translator has a built in driver (GoogleTranslatorDriver) to translate text.
and to store it in local this translator also has a manager (TranslatorFileManager) to do it...
for advanced usage you can simply add new sources like driver, manager just follow the interfaces of this class

## Direct Usage of GoogleTranslatorEngine

```php
<?php
// index.php

use VulcanPhp\Translator\Engine\Google\GoogleTranslator;

require_once __DIR__ . '/vendor/autoload.php';

// create a instance of google translator engine
$engine = GoogleTranslator::create();

// translate a text from google
// translate with all languages that supported by google translator
var_dump($engine->translateFromString('Today is a sunny day', 'en', 'bn'));
var_dump($engine->translateFromString('Today is a sunny day', 'en', 'fr'));
var_dump($engine->translateFromString('Today is a sunny day', 'en', 'es'));

// translate multiple text at a time from google
var_dump(
    $engine->translateFromArray(
        [
            'Today is a sunny day',
            'What should i wear today?',
        ],
        'en',
        'bn'
    )
);

// ...
```

## Create Multiple Translator Instance for Multiple Languages
```php
<?php
// index.php

use VulcanPhp\Translator\Drivers\GoogleTranslatorDriver;
use VulcanPhp\Translator\Manager\TranslatorFileManager;
use VulcanPhp\Translator\Translator;

require_once __DIR__ . '/vendor/autoload.php';

// create a translator for bengali language
$Translator_BN = Translator::create(
        new GoogleTranslatorDriver(
            new TranslatorFileManager([
                'source'    => 'en',
                'convert'   => 'bn',
                'local_dir' => __DIR__ . '/translate',
            ])
        )
    )
    ->getDriver();

var_dump($Translator_BN->translate('Hello World'));

// create another translator for spanish language 
$Translator_ES = Translator::create(
        new GoogleTranslatorDriver(
            new TranslatorFileManager([
                'source'    => 'en',
                'convert'   => 'es',
                'local_dir' => __DIR__ . '/translate',
            ])
        )
    )
    ->getDriver();

var_dump($Translator_ES->translate('Hello World'));

// ...
```
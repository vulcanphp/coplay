# InputMaster
Input Master is a simple and easy PHP Input Data Manager


## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Sweet View.

```bash
$ composer require vulcanphp/inputmaster
```

## Basic Usage
```php
<?php

use VulcanPhp\InputMaster\Request;

require __DIR__ . '/vendor/autoload.php';

// Full Request Instance
$request = new Request;

// Response Instance
$response = $request->getResponse();

// Input Handler
$input = $request->inputHandler();

// Current Url
$currentUrl = $request->getUrl();

// Root Url
$rootUrl = $request->rootUrl();

```

### Full Documentation is COMING SOON
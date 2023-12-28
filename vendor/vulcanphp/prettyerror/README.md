# Pretty Error
Pretty Error is a simplified way to handle PHP errors with a pretty interface

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install SweetView.

```bash
$ composer require vulcanphp/prettyerror
```
## Register Pretty Error

```php
<?php

use VulcanPhp\PrettyError\PrettyError;

require_once __DIR__ . '/vendor/autoload.php';

// register PrettyError for Development Mode
PrettyError::register(PrettyError::ENV_DEVELOPMENT);

// register PrettyError for Production Mode
PrettyError::register(PrettyError::ENV_PRODUCTION);

// ...
```
That's it..
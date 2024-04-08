# Sweet View
Sweet View is a lightweight and powerful PHP template engine that unblocks all the most common features in handling a View in PHP.

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Sweet View.

```bash
$ composer require vulcanphp/sweetview
```

## Basic Usage
After Installing Sweet View require compoer autoloader then you can simple use it calling view() function

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

echo view('welcome', ['message' => 'Welcome to Sweet View']);

// ...
```

Now, Create a View Template and Layout file into: /resources/views/
```php
<?php
// view: welcome.php

$this->layout('layout.master')
  ->block('title', 'Welcome to Sweet View');

?>

<h1><?= $message ?></h1>

```
Create a Layout to: /resources/views/layout/
```php
<?php
// layout: master.php

$this->minified(true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->getBlock('title', 'Welcome Home') ?></title>
</head>
<body>
    <main class="container">
        {{content}}
    </main>
</body>
</html>
```

Thats it

## Sweet View available methods
- layout(string $path): self
- block(string $name, $value): self
- hasBlock(string $name): bool
- getBlock(string $name)
- with(array $params = []): self
- minified(bool $default = true): self
- include(string $path, array $params = []): self
- require(string $path, array $params = []): self
- includeOnce(string $path, array $params = []): self
- requireOnce(string $path, array $params = []): self
- component(string $path, array $params = []): self

```php
<?php
// view: blogs.php
$this
  // call a layout
  ->layout('layout.master')

  // add a new block
  ->block('title', 'Blogs: Sweet View')

  // declare variable all over the view
  ->with(['theme' => 'dark', 'sidebar' => true])

  // require template part
  ->require('layout.breadcrumb')

  // include template part
  ->include('layout.hero')

?>

<!-- include_once template part -->
<?php $this->includeOnce('layout.cta')?>

<div class="row">
  <?php foreach($blogs as $blog): ?>
    <div class="col-md-4">
      <!-- include template component -->
      <?php $this->component('components.blog', ['blog' => $blog]) ?>
    </div>
  <?php endforeach ?>
</div>

<!-- require_once template part -->
<?php $this->requireOnce('includes.contact')?>

```

## Sweet View Advanced Usage
```php
<?php
// controller: home.php

use VulcanPhp\SweetView\Drivers\HtmlDriver;
use VulcanPhp\SweetView\SweetView;

// create a new SweetView instance
$view = SweetView::create(new HtmlDriver);

// get view engine
$engine = $view->getDriver()->getEngine();

// change resource directory
$engine->resourceDir(__DIR__ . '/resources/views/');

// render output
echo $engine->render('welcome', ['message' => 'Welcome to Sweet View']);

// ..

```

## Sweet View Html Engine Advanced Usage
```php
<?php
// controller: home.php

use VulcanPhp\SweetView\Engine\Html\Html;

// create a Html instance
$html = Html::load('welcome', 'master');
// Note: layout is optional

// or specify different folder
$html = Html::load(
  __DIR__ . '/resources/views/welcome.php',
  __DIR__ . '/resources/views/layout/master.php'
);

// change resource dir
$html->resourceDir(__DIR__ . '/directory/path/');

// set view file extension
$html->extension('.sweet');

// clean prevouse output
$html->clean();

// enable minification output
$html->minified();

// set global variable in view
$html->with(['theme' => 'dark', 'sidebar' => true]);

// render html output
echo $html->render(['title' => 'Sweet View']);

// ..

```
## Sweet View Use Html Meta

```php
<?php
// layout: master.php

$this
  // set default meta
  ->setMeta('charset', 'utf-8')
  ->setMeta('robots', 'all')
  ->setMeta('sitename', '{sitename}')

?>

<head>
  <!-- render generated meta on head tag -->
  <?= $this->siteMeta() ?>
</head>

// ..

```

```php
<?php
// view: welcome.php

$this->setupMeta([
  // set meta title
  'title' => 'SweetView',

  // set meta description
  'description' => 'Simple & Powerful Php Template Engine',

  // set og:image
  'image' => 'https://domain.com/images/photo.png',
]);

// ..

```

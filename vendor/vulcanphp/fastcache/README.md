# Fast Cache
Fast Cache is a PHP simple and super fast caching system which make PHP Application faster

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install Fast Cache

```bash
$ composer require vulcanphp/fastcache
```

## Fast Cache Usage
After Installing Fast Cache initialize it then you can simply use it allover in your application

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

// initialize global cache
cache_init([
    'path' => __DIR__ . '/tmp',
    'extension' => '.cache'
]);

// create a cache for logged users
$cache = cache('logged_users');

// add a user into cache
$cache->store(
    // cache key
    'id_1',
    // cache data
    ['name' => 'John Doe', 'email' => 'jhon@mail.com'],
    // expire in or, null to not expire
    '10 minutes', // use minutes, hours, days etc with number
);

// to simply check a record exists in cache
$cache->hasCache('id_1');

// but, to check if a record is exists with not expired yet 
$cache->hasCache('id_1', true);

// to simply retrieve a record from cache
$user = $cache->retrieve('id_1');

// but, to retrieve a record which is not expired 
$user = $cache->retrieve('id_1', true);

// to simply retrieve all record from cache
$users = $cache->retrieveAll();

// but, to retrieve all record which are not expired 
$users = $cache->retrieveAll(true);

// load a user from cache and if not exists then store and return
// this will check if a record exists which along with not expired yet
// else it will store and return the record
$user = $cache->load('id_1', fn() => ['name' => 'John Doe', 'email' => 'jhon@mail.com'], '10 minutes');

// simple erase records from cache
$cache->erase('id_1');

// or multiple
$cache->erase(['id_1', 'id_2']);

// to delete this cache file
$cache->flush();

// to remove all the available caches
cache_engine()->flush();

// ...
```

### Available Methods to Fast Cache Handler
- hasCache(string $key, bool $eraseExpired = false): bool
- retrieve($keys, bool $eraseExpired = false): mixed
- retrieveAll(bool $eraseExpired = false): array
- load(string $key, callable $callback,  ?string $expire = null)
- store(string $key, $data,  ?string $expire = null): self
- erase($keys): self
- eraseExpired(): self
- flush(): self
- close(): self

## Usage of Site Cache
This Caching Method first store the html output for a certain time that the application serve
and next time the application will be server by cache
NOTE: Site Cache is useful but you need to carefully configure it according to your application

```php
<?php
// index.php

require_once __DIR__ . '/vendor/autoload.php';

// initialize site cache on root file after including vendor/autoload.php 
init_site_cache([
    // Cache Configuration
    // --------------------

    // exclude some path from being cached
    'except'    => ['/admin/*'],
    // include some path which are exclude
    'include'   => ['/admin/login/*'],
    'methods'   => ['get'],
    'useragent' => [
        // match useragent only those will be served from cache
        // keep it empty to allow all useragent
    ],
    // allow bots to use cache or not, true to enable bots
    'bots'      => false,
    // set a tmp_dir location to store caches
    'tmp_dir'   => __DIR__ . '/tmp/caches',
    // set a duration for the cache
    'expire'    => '10 minutes',
    // set cache extension
    'extension' => '.html',
    // minify caches output
    'minify'    => false,
    // limit of total store caches
    'limit'     => 100,
    // show cache information as comment on source-code
    'metadata'  => true,
], [
    // Cache Conditions for User, if failed it will ignore
    // ----------------------------------------------------
    
    // when $_SESSION does not have a user value EX: $_SESSION['user'] == null
    // basically if a user is logged in, then this cache will be ignored and serve fresh contents from application
    'session.user'  => null,
    'cookie.user'   => null,
    // Available all global variable such as: $_SESSION, $_COOKIE, $_SERVER etc as session, cookie, server ...
    // Available Condition is equal and not equal, Example: session.user (is equal to) and !session.user (is not equal to)
]);

// serve from site cache if exists (required)
serve_site_cache();

// your application codes <START>

// For Example:

$app = new MyApp();
$app->router->get('/', ['Home', 'index']);
$app->run();

echo $app->response()->getBody();

// your application codes <END>

exit;


// ...
```
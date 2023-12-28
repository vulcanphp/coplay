# Easy Curl for PHP
Easy Curl is a helper class for PHP that make the HTTP requests easier

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHP Easy Curl

```bash
$ composer require vulcanphp/easycurl
```

## PHP Easy Curl Basic Usage

```php
<?php
// index.php

use VulcanPhp\EasyCurl\EasyCurl;

require_once __DIR__ . '/vendor/autoload.php';

// make a http get, post, put, patch, and delete request
var_dump(
    EasyCurl::get('https://jsonplaceholder.typicode.com/posts')
        ->getJson()
);

// can make every http request statically
var_dump(
    EasyCurl::put('https://jsonplaceholder.typicode.com/posts/1')
        ->getBody()
);

// create a Easy Curl instance
$http = EasyCurl::create();

// set curl options
$http->setOption(CURLOPT_TIMEOUT, 20);

// set header
$http->setHeader('Authorization', 'Bearer {token}');

// set postfields 
$http->setPostFields(['name' => 'John Doe', 'email' => 'john@mail.com']);

// send this request
$resp = $http->post('http://your-location.domain/path');

// print the response
var_dump($resp);

// ...
```

## Usage of PHP Easy Curl Response
```php
<?php
// index.php
use VulcanPhp\EasyCurl\EasyCurl;

require_once __DIR__ . '/vendor/autoload.php';

$resp = EasyCurl::get('https://jsonplaceholder.typicode.com/posts');

// get the curl output in array
var_dump($resp->getResponse());

// get the curl status
var_dump($resp->getStatus());

// get response body
var_dump($resp->getBody());

// if the output is json format we can convert it with array
var_dump($resp->getJson()); 

// get output content length
var_dump($resp->getLength());

// get curl last effective url
var_dump($resp->getLastUrl());

// Tip: all methods can be called without get word, EX: $resp->body() is equal to $resp->getBody()
```
### Quick Usage
```php
<?php
// index.php

use VulcanPhp\EasyCurl\EasyCurl;

require_once __DIR__ . '/vendor/autoload.php';

// quick create a curl instance with static/non-static methods
$http = EasyCurl::options([
    CURLOPT_TIMEOUT => 20,
    // all curl_setopt() options 
]);

// send method is equal to get method, or you can use any http methods
$resp = $http->send('http://domain.com/path');

// download a file
$status = EasyCurl::downloadFile(__DIR__ . '/test.txt')
    ->get('http://domain.com/path')
    ->status();

var_dump($status);

// setup a proxy in easy curl
$http = EasyCurl::proxy([
    'ip'    => '127.0.0.1', // proxy ip
    'port'  => '8080', // proxy port
    'auth'  => 'user:pass', // basic proxy auth
]);

var_dump($http->get('http://domain.com/path'));

// ...
```

### Available Methods to EasyCurl Driver
- send(string $url, array $params = []): ICurlResponse
- get(string $url, array $params = []): ICurlResponse
- post(string $url, array $params = []): ICurlResponse
- put(string $url, array $params = []): ICurlResponse
- patch(string $url, array $params = []): ICurlResponse
- delete(string $url, array $params = []): ICurlResponse
- setOption(int $key, mixed $value): ICurlDriver
- setOptions(array $options): ICurlDriver
- setUseragent(string $useragent): ICurlDriver
- setHeader(string $key, string $value): ICurlDriver
- setHeaders(array $headers): ICurlDriver
- setCookieFile(string $filepath): ICurlDriver
- setDownloadFile(string $filepath, bool $override = false): ICurlDriver
- setPostFields(mixed $fields): ICurlDriver
- setProxy(array $proxy): ICurlDriver

Note: You can call these every methods statically when creating a Easy Curl Instance.

and you can also call every set methods without (set) word, EX: EasyCurl::option() is equal to EasyCurl::setOption()
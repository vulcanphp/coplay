# PHP Router
PHP Router is a powerful, secure, simple, and quick routing system for PHP Application or REST APIs

## Installation

It's recommended that you use [Composer](https://getcomposer.org/) to install PHP Router

```bash
$ composer require vulcanphp/phprouter
```

## Basic Usage
After Installing PHP Router initialize it in your application then you can simply use it allover on your application

```php
<?php

use VulcanPhp\PhpRouter\Router;
use VulcanPhp\PhpRouter\Route;

require_once __DIR__ . '/vendor/autoload.php';

// initialize PHP Router
$router = Router::init();

// set default middleware to router
$router->setMiddlewares([Csrf::class]);

/**
 * methods: get, post, put, patch, delete, options
 * all are available for both static or non-static
 */

// add a route with closure callback
$router->get('/', function(){
    echo 'Welcome to PHP Router';
});

// add route statically with array callback class
Route::get('/user', [User::class, 'index']);

// add a route for both get or post http request
Route::form('/user/create', [User::class, 'create']);

// add a route for any http request
Route::any('/user/find', [User::class, 'find']);

// add a route with defining a $id parameter
Route::get('/user/{id}', function($id){
    var_dump($id);
});

// add route with matched http request
Route::match(['post', 'options'], '/user/ajax', [User::class, 'ajax']);

// add a resource route for book
Route::resource('book', Book::class);

// add a redirect route with http code: 301
Route::redirect('/here', '/there', 301);

// if your application has a view() function for vulcanphp/sweet-view then you can simply add a view route
Route::view('/docs', 'docs');

// add a route with a specific regex
Route::regex('%^/(.+)$%', 'get', fn($slug) => var_dump($slug));

// set a fallback to this router
$router->setFallback(function() {
    echo 'Page Does not Found';
    exit;
});

// resolve current router
echo $router->resolve();
// ...
```
## Use With Grouped Routes

```php
<?php

use VulcanPhp\PhpRouter\Route;

// add a simple router group
Route::group(['prefix' => 'user'], function(){
    // assign routes in this group
    Route::get('/create', '{callback}');

    // add route 
    Route::get('/show/{id}', function($id){
        var_dump($id);
    });
});

// include routes from external file
Route::group(
    ['prefix' => 'api/v1', 'middlewares' => [Cros::class, JWTGuard::class]],
    // it will fetch all the routes from api.php and assign to this group
    __DIR__ . '/routes/api.php', 
);

// use nested groups
Route::group(['prefix' => 'admin'], function(){
    Route::get('/', [Dashboard::class, 'index']);
    
    // create a new group under parent group
    Route::group(['prefix' => 'user'], function(){
        Route::get('/', [User::class, 'index']);
        Route::get('/create', [User::class, 'create']);
        Route::get('/{id}/edit/', [User::class, 'end']);
    });

    // add a resource route under this group
    Route::resource('book', Book::class);
});
// ...
```

## PHP Router Advanced Usage

```php
<?php

use VulcanPhp\PhpRouter\Router;
use VulcanPhp\PhpRouter\Route;
use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Http\Input\InputHandler;

/** 
 * usage of reflection parameters
 * available: Request, Response, InputHandler, Url, IRoute
 */
Route::form('/login', function(Request $request, InputHandler $input, Response $response) {
    if($request->isPostBack()){
        if($input->exists(['username', 'password'])){
            var_dump($input->all());
        }else{
            return $response->back();
        }
    }

    echo 'hello from login';
})
    ->name('login')
    ->middleware(LoginGuard::class);

// add a new reflection parameter
Router::$instance->setFilters(
    Router::FILTER['reflection_parameters'],
    function($parameters){
        
        $parameters[] = [
            'parameter' => BaseModel::class,
            'callback' => function($param, $params, $key, $value) {
                return $param->getType()->getName()::findOrFail([
                    $param->getType()->getName()::primaryKey() => $value
                ]);
            }
        ];

        return $parameters;
    }
);

// usage of custom reflection parameter
Route::get('/user/{id}', function(User $user){
    var_dump($user);
});

// ...
```

## Example Middleware

```php
<?php

namespace MyApp\Middlewares;

use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;

class MyMiddleware implements IMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        // do whatever ..
    }
}
```

## Example Csrf Verified

```php
<?php

namespace MyApp\Middlewares;

use VulcanPhp\PhpRouter\Security\BaseCsrfVerifier as IMiddleware;
use VulcanPhp\PhpRouter\Security\Token\SessionTokenProvider as TokenProvider;

class Csrf extends IMiddleware
{
    /**
     * CSRF validation will be ignored on the following urls.
     */
    protected $except = ['/api/*'];

    public function __construct()
    {
        $this->setTokenProvider(new TokenProvider);
    }
}

```

## Example Resource Controller

```php
<?php

namespace MyApp\Controller;

use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Routing\Interfaces\IResource;

class MyResourceController implements IResource
{
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function index()
    {
        // code ..
    }

    public function show($id)
    {
        // code ..
    }

    public function store()
    {
        // code ..
    }

    public function create()
    {
        // code ..
    }

    public function edit($id)
    {
        // code ..
    }

    public function update($id)
    {
        // code ..
    }

    public function destroy($id)
    {
        // code ..
    }
}
```
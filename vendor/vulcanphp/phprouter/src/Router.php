<?php

namespace VulcanPhp\PhpRouter;

use VulcanPhp\PhpRouter\Callback\CallbackHandler;
use VulcanPhp\PhpRouter\Http\Url;
use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Http\Response;
use VulcanPhp\PhpRouter\Routing\IRoute;
use VulcanPhp\PhpRouter\Routing\Exceptions\NotFoundException;
use VulcanPhp\PhpRouter\Routing\Exceptions\RouterException;
use VulcanPhp\PhpRouter\Routing\Exceptions\UnsupportedMethodException;
use VulcanPhp\PhpRouter\Routing\ResourceRoute;
use VulcanPhp\PhpRouter\Routing\RouteDispatcher;
use VulcanPhp\PhpRouter\Security\BaseCsrfVerifier;
use VulcanPhp\PhpRouter\Security\Exceptions\MiddlewareException;

/**
 * PHP Router is a secure, simple, and quick routing system for php application
 * 
 * @author Shahin Moyshan
 * @version 1.0
 * @link https://github.com/vulcanphp/phprouter
 * @package VulcanPhp\PhpRouter
 */
class Router
{
    public array $middlewares = [], $filters = [], $collection = [
        'routes' => [],
        'groups' => []
    ];
    public static Router $instance;
    public Request $request;
    public Response $response;
    public $fallback;

    /** 
     * Router Filter
     * @param array
     */
    public const FILTER = [
        'routes' => 1,
        'route' => 2,
        'middlewares' => 3,
        'parameters' => 4,
        'reflection_parameters' => 5
    ];

    public function __construct()
    {
        self::$instance = $this;

        $this->request = Request::create();
        $this->response = Response::create();
    }

    public static function init(...$args): Router
    {
        return new Router(...$args);
    }

    /**
     * Resolve current Http Request and Dispatch Matcher Route
     * 
     * @return mixed 
     * @throws UnsupportMethodException 
     * @throws MiddlewareException 
     * @throws RouterException 
     * @throws NotFoundException 
     */
    public function resolve()
    {
        // current http method and path
        $method = $this->request->getMethod();
        $url    = $this->request->getUrl();

        // loop all registared routes
        foreach ($this->getRoutes() as $route) {
            // match current active route
            if (preg_match($route->getRegex(), $url->getPath(), $matches) === 1) {

                // filter matched route
                $route = $this->applyFilters(self::FILTER['route'], $route);

                // compare server request method with route's allowed http methods
                if (!in_array($method, $route->getMethods(), true)) {
                    $this->response->httpCode(405);
                    throw new UnsupportedMethodException(
                        sprintf('Unsupported Method %s, (%s) only supported for this route', $method, join(', ', $route->getMethods()))
                    );
                }

                // create route dispatcher
                $dispatcher = new RouteDispatcher($this);

                // utilize method required paramiters
                $route->setParameters(
                    $this->applyFilters(self::FILTER['parameters'], $this->utilizeRouteParam($matches, $route))
                );

                // set Url to route
                $route->setUrl($url);

                // dispatch route callback
                return $dispatcher->dispatch($route);
            }
        }

        // call fallback
        if (isset($this->fallback) && CallbackHandler::exists($this->fallback)) {
            CallbackHandler::load($this->fallback);
        }

        // not found any route for this path
        throw new NotFoundException('the route does not matched..');
    }

    /**
     * Find a route from collection
     * 
     * @param string $name 
     * @param mixed $params 
     * @return IRoute 
     * @throws NotFoundException 
     */
    public function route(string $name, $params = null): IRoute
    {
        foreach ($this->getRoutes() as $_route) {
            if (in_array($name, (array) $_route->getName())) {
                // filter matched route
                $route = $this->applyFilters(self::FILTER['route'], clone $_route);

                if ($route instanceof ResourceRoute) {
                    $action       = explode('.', $name);
                    $action       = end($action);
                    $path         = preg_replace('/\/{[^}]+}/', '', $route->getPath());
                    $resourceUrls = [
                        'index'   => '',
                        'create'  => 'create',
                        'store'   => '',
                        'show'    => '{id}',
                        'edit'    => '{id}/edit',
                        'update'  => '{id}',
                        'destroy' => '{id}',
                        'data'    => 'data',
                        'options' => 'options',
                    ];

                    if (!is_array($params) && intval($params) == $params) {
                        $params = ['id' => $params];
                    }

                    $route->setPath($path . $resourceUrls[$action]);
                }

                if ($params !== null) {
                    $params = (array) $params;
                    $keys   = $this->routeParamKeys($route->getPath());

                    foreach ((array) $keys[1] as $key => $param) {
                        if ((isset($params[$param]) || isset($params[$key])) === false) {
                            continue;
                        }

                        $route->setPath(str_ireplace('{' . $param . '}', ($params[$param] ?? $params[$key]), $route->getPath()));
                    }
                }

                return $route->setUrl(new Url(trim($this->request->rootUrl()->absoluteUrl(), '/') . $route->getPath()));
            }
        }

        // not found any route for this path
        throw new NotFoundException('Route: ' . $name . ' does not exists');
    }

    protected function getRoutes(): array
    {
        return $this->applyFilters(self::FILTER['routes'], (array) $this->collection['routes'] ?? []);
    }

    public function __call($name, $arguments)
    {
        return call_user_func([Route::class, $name], ...$arguments);
    }

    public function getMiddlewares(): array
    {
        return $this->applyFilters(self::FILTER['middlewares'], (array) $this->middlewares ?? []);
    }

    public function setMiddlewares($middlewares): self
    {
        $this->middlewares = (array) $middlewares;

        return $this;
    }

    public function setFallback($callback): self
    {
        $this->fallback = $callback;
        return $this;
    }

    public function getFilters(int $filter): array
    {
        return $this->filters[$filter] ?? [];
    }

    public function setFilters(int $filter, $callback): self
    {
        if (!isset($this->filters[$filter])) {
            $this->filters[$filter] = [];
        }

        $this->filters[$filter] = array_merge($this->filters[$filter], [$callback]);

        return $this;
    }

    public function applyFilters(int $filter, $data)
    {
        foreach ($this->getFilters($filter) as $filter) {
            if (CallbackHandler::exists($filter)) {
                $data = CallbackHandler::load($filter, $data);
            }
        }

        return $data;
    }

    public function getCsrfToken(): ?string
    {
        foreach ($this->getMiddlewares() as $middleware) {
            $middleware = CallbackHandler::create($middleware);
            if ($middleware instanceof BaseCsrfVerifier) {
                return $middleware->getTokenProvider()->getToken();
            }
        }

        return null;
    }

    protected function utilizeRouteParam(array $params, IRoute $route): array
    {
        unset($params[0]);

        $params = array_merge(...array_map(fn ($param) => explode('/', $param), array_filter(array_map(fn ($param) => trim($param, '/'), $params))));
        $keys   = $this->routeParamKeys($route->getPath())[1];

        return count($params) == count($keys) ? array_combine($keys, $params) : $params;
    }

    protected function routeParamKeys(string $path): array
    {
        preg_match_all('/{(.*)}/U', $path, $keys);
        return array_map(fn ($key) => str_replace(['{', '?', '}'], '', $key), (array) $keys);
    }
}

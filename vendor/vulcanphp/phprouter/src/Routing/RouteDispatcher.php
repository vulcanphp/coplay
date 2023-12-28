<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Callback\CallbackHandler;
use VulcanPhp\PhpRouter\Http\Request;
use VulcanPhp\PhpRouter\Routing\IRoute;
use VulcanPhp\PhpRouter\Router;
use VulcanPhp\PhpRouter\Routing\Exceptions\RouterException;
use VulcanPhp\PhpRouter\Routing\Interfaces\IResource;
use VulcanPhp\PhpRouter\Routing\ResourceRoute;
use VulcanPhp\PhpRouter\Security\Exceptions\MiddlewareException;
use VulcanPhp\PhpRouter\Security\Interfaces\IMiddleware;

class RouteDispatcher
{
    protected Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Dispatch Current Matched Route
     * 
     * @param IRoute $route 
     * @return mixed 
     * @throws MiddlewareException 
     * @throws RouterException 
     */
    public function dispatch(IRoute $route)
    {
        // call route dispatch events
        $this->triggerEvent(IRoute::EVENTS['dispatch'], $route);

        // router middleware collection
        $middlewares = array_merge((array) $this->router->getMiddlewares(), (array) $route->getMiddlewares());

        // check if resource callback or not
        if (is_string($route->getCallback()) && class_exists($route->getCallback())) {
            $route->setCallback([$route->getCallback()]);
        }

        if (is_array($route->getCallback())) {
            // call route callback construct event
            $this->triggerEvent(IRoute::EVENTS['construct'], $route);

            // create new instance of the controller
            $route->setCallback([
                CallbackHandler::create(
                    $route->getCallback()[0],
                    ...CallbackHandler::reflect(
                        [],
                        [$route->getCallback()[0], '__construct'],
                        array_merge($this->getReflectionParameters(), [$route])
                    )
                ),
                $route->getCallback()[1] ?? null
            ]);

            // trigger controller middlewares before load
            if (CallbackHandler::exists([$route->getCallback()[0], 'getMiddlewares'])) {
                $middlewares = array_merge($middlewares, (array) $route->getCallback()[0]->getMiddlewares());
            }
        }

        // set all middlewares to route
        $route->setMiddlewares($middlewares);

        // trigger route middleware event
        $this->triggerEvent(IRoute::EVENTS['middlewares'], $route);

        // call all middleware
        $this->callMiddlewares($route->getMiddlewares());

        // dispatch resource router callback
        if ($route instanceof ResourceRoute) {
            // check if controller implemented IResourceController
            if (!($route->getCallback()[0] ?? null) instanceof IResource) {
                throw new RouterException(
                    CallbackHandler::name($route->getCallback()) . ' must be implement ' . IResource::class
                );
            }

            list($method, $params, $action, $call_method) = [$this->router->request->getMethod(), $route->getParameters(), $route->getAction(), 'index'];

            // dispatch resource current method
            if ($method === Request::REQUEST_TYPE_OPTIONS && $params[$action] == 'options') {
                $call_method = 'options';
            } elseif ($method === Request::REQUEST_TYPE_DELETE && isset($params[$action])) {
                $call_method = 'destroy';
            } elseif (in_array($method, [Request::REQUEST_TYPE_PUT, Request::REQUEST_TYPE_PATCH]) && isset($params[$action])) {
                $call_method = 'update';
            } elseif ($method === Request::REQUEST_TYPE_GET && isset($params[0]) && isset($params[1]) && $params[1] === 'edit') {
                $call_method = 'edit';
            } elseif ($method === Request::REQUEST_TYPE_GET && isset($params[$action]) && $params[$action] === 'create') {
                $call_method = 'create';
            } elseif ($method === Request::REQUEST_TYPE_GET && isset($params[$action])) {
                $call_method = 'show';
            } elseif ($method === Request::REQUEST_TYPE_POST && isset($params[$action]) && $params[$action] === 'data') {
                $call_method = 'data';
            } elseif ($method === Request::REQUEST_TYPE_POST) {
                $call_method = 'store';
            }

            // set new callback method
            $route->setCallback([$route->getCallback()[0], $call_method]);
        }

        // escape grouped/nested named parameter
        $route->setParameters($this->escapeNestedParameters($route->getParameters()));

        // trigger route middleware event
        $this->triggerEvent(IRoute::EVENTS['callback'], $route);

        // set current route
        $this->router->request->setRoute($route);

        // load router callback
        return CallbackHandler::load(
            $route->getCallback(),
            ...CallbackHandler::reflect(
                $route->getParameters(),
                $route->getCallback(),
                array_merge($this->getReflectionParameters(), [$route])
            )
        );
    }

    public function callMiddlewares($middlewares): void
    {
        foreach ((array) $middlewares as $middleware) {
            $class = CallbackHandler::create($middleware);

            if (!$class instanceof IMiddleware) {
                throw new MiddlewareException($middleware . ' must be implement interface: ' . IMiddleware::class);
            }

            CallbackHandler::load([$class, 'handle'], $this->router->request, $this->router->response);
        }
    }

    public function triggerEvent(int $event, IRoute &$route): void
    {
        foreach ($route->getEvents($event) as $event) {
            if (CallbackHandler::exists($event)) {
                CallbackHandler::load($event, $route);
            }
        }
    }

    protected function escapeNestedParameters(array $params): array
    {
        $extract = [];
        foreach ($params as $key => $value) {
            if (strrpos($key, '.') !== false) {
                $extract[substr($key, strrpos($key, '.') + 1)] = $value;
            } else {
                $extract[$key] = $value;
            }
        }

        return $extract;
    }

    protected function getReflectionParameters(): array
    {
        return $this->router->applyFilters(
            $this->router::FILTER['reflection_parameters'],
            [
                $this->router->request,
                $this->router->request->getUrl(),
                $this->router->request->inputHandler(),
                $this->router->response,
            ]
        );
    }
}

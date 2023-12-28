<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Router;

trait RouteCollection
{
    public function attachRoute(IRoute $route): void
    {
        if (!empty(Router::$instance->collection['groups'])) {
            // apply all grouped routed
            foreach (array_reverse((array) Router::$instance->collection['groups']) as $group) {
                $this->attachGroupedOptions($group, $route);
            }
        }

        array_push(Router::$instance->collection['routes'], $route);
    }

    /**
     * [dispatchRouterGroup description]
     * 
     * @param  IRoute &$route [description]
     * @return [type]         [description]
     */
    protected function attachGroupedOptions(GroupedRoute $group, IRoute &$route): void
    {
        if ($group->getPrefix() !== null && !empty($group->getPrefix())) {
            $route->setPath(sprintf('%s/%s', rtrim($group->getPrefix(), '/'), ltrim($route->getPath(), '/')))
                ->generateRegex();
        }

        if (!empty($group->getMethods())) {
            $route->setMethods(array_merge($group->getMethods(), $route->getMethods()));
        }

        if (!empty($group->getMiddlewares())) {
            $route->setMiddlewares(array_merge($group->getMiddlewares(), $route->getMiddlewares()));
        }

        if ($group->getName() !== null && !empty($group->getName())) {
            if ($route instanceof ResourceRoute) {
                $route->setAction($group->getName() . $route->getAction())
                    ->setPath(preg_replace('/\/{[^}]+}/', '/{' . $route->getAction() . '}', $route->getPath()))
                    ->generateNames();
            } else {
                $route->setName($group->getName() . $route->getName());
            }
        }
    }

    /**
     * [registerGroupRoute description]
     * 
     * @return [type] [description]
     */
    public function registerGroupRoute(GroupedRoute $group): void
    {
        array_push(Router::$instance->collection['groups'], $group);
    }

    /**
     * [unregisterGroupRoute description]
     * 
     * @return [type] [description]
     */
    public function unregisterGroupRoute(): void
    {
        array_pop(Router::$instance->collection['groups']);
    }
}

<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Callback\CallbackHandler;
use VulcanPhp\PhpRouter\Callback\UniversalCall;
use VulcanPhp\PhpRouter\Routing\Exceptions\RouterException;

class GroupedRoute
{
    use UniversalCall, RouteCollection;

    /**
     * Accepted HTTP methods for this route.
     *
     * @var string[]
     */
    protected array $methods;

    /**
     * The name of this route, used for reversed routing
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $prefix;

    /**
     * Array containing middlewares passed through request URL
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var callback
     */
    protected $callback;

    public function __construct(array $settings = [], $callback = null)
    {
        $this->setPrefix($settings['prefix'] ?? null)
            ->setMiddlewares($settings['middlewares'] ?? null)
            ->setMethods($settings['methods'] ?? null)
            ->setName($settings['name'] ?? null)
            ->setCallback($callback);
    }

    public function getMethods(): array
    {
        return (array) $this->methods ?? [];
    }

    public function setMethods($methods): self
    {
        $this->methods = (array) $methods;
        return $this;
    }

    public function getMiddlewares(): array
    {
        return (array) $this->middlewares ?? [];
    }

    public function setMiddlewares($middlewares): self
    {
        $this->middlewares = (array) $middlewares;

        return $this;
    }

    public function getName()
    {
        return $this->name ?? null;
    }

    public function setName($name): self
    {
        $this->name = (string) $name;
        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix ?? null;
    }

    public function setPrefix($prefix): self
    {
        if ($prefix !== null) {
            $this->prefix = sprintf('/%s%s', trim($prefix, '/'), $prefix !== '/' ? '/' : '');
        }
        return $this;
    }

    public function getCallback()
    {
        return $this->callback ?? null;
    }

    public function setCallback($callback): self
    {
        $this->callback = $callback;
        return $this;
    }

    public function __destruct()
    {
        $this->registerGroupRoute($this);

        $callback = $this->getCallback();

        if (is_string($callback) && file_exists($callback)) {
            require $callback;
        } elseif (CallbackHandler::exists($callback)) {
            CallbackHandler::load($callback);
        } else {
            throw new RouterException('failed to load routes from: ' . $callback);
        }

        $this->unregisterGroupRoute();
    }
}

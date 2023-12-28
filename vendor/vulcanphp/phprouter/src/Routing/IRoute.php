<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Callback\UniversalCall;
use VulcanPhp\PhpRouter\Http\Url;

abstract class IRoute
{
    use UniversalCall, RouteCollection;

    /** 
     * Router Events
     * @param array
     */
    public const EVENTS = [
        'dispatch' => 1,
        'construct' => 2,
        'middlewares' => 3,
        'callback' => 4,
    ];

    /**
     * PATH of this Route
     * @var string
     */
    protected string $path;

    /**
     * REGEX of this Route
     * @var string
     */
    protected string $regex;

    /**
     * Accepted HTTP methods for this route.
     *
     * @var string[]
     */
    protected array $methods;

    /**
     * The name of this route, used for reversed routing
     * @var mixed
     */
    protected $name;

    /**
     * @var string
     */
    protected string $prefix;

    /**
     * Array containing parameters passed through request URL
     * @var array
     */
    protected array $parameters = [];

    /**
     * Array containing middlewares passed through request URL
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var mixed
     */
    protected $callback;

    /**
     * @var mixed
     */
    protected $events = [];

    /**
     * @var mixed
     */
    protected Url $url;

    /**
     * generate route path regex
     * @return IRoute 
     */
    abstract public function generateRegex(): self;

    public function getPath()
    {
        return (string) $this->path ?? '';
    }

    public function setPath(string $path): self
    {
        $this->path = sprintf('/%s%s', trim($path, '/'), $path !== '/' ? '/' : '');
        return $this;
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

    public function getParameters(): array
    {
        return (array) $this->parameters ?? [];
    }

    public function setParameters($parameters): self
    {
        $this->parameters = (array) $parameters;

        return $this;
    }

    public function getName()
    {
        return $this->name ?? null;
    }

    public function setName($name): self
    {
        $this->name = $name;
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

    public function getRegex()
    {
        return (string) $this->regex ?? '';
    }

    public function setRegex($regex): self
    {
        $this->regex = (string) $regex;
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

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function setUrl(Url $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getEvents(int $event): array
    {
        return $this->events[$event] ?? [];
    }

    public function setEvents(int $event, callable $callback): self
    {
        if (!isset($this->events[$event])) {
            $this->events[$event] = [];
        }

        $this->events[$event] = array_merge($this->events[$event], [$callback]);

        return $this;
    }

    public function __destruct()
    {
        $this->attachRoute($this);
    }
}

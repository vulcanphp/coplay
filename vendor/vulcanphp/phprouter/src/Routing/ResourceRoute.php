<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Routing\IRoute;

class ResourceRoute extends IRoute
{
    /**
     * PATH of this Route
     * @var string
     */
    protected string $action;

    /**
     * @param       $resource
     * @param array $config
     */
    public function __construct(string $path, string $controller)
    {
        $this->setAction($path)
            ->setPath(sprintf('%s/{%s}', $path, $this->getAction()))
            ->setMethods(['get', 'post', 'put', 'patch', 'options', 'delete'])
            ->setCallback($controller)
            ->generateNames()
            ->generateRegex();
    }

    public function generateRegex(): self
    {
        $this->regex = '%^' . preg_replace('/\/{[^}]+}/', '', $this->getPath()) . '(.*?)[\/]?$%';
        return $this;
    }

    public function generateNames(): self
    {
        $name = $this->getAction();

        return $this->setName([
            sprintf('%s.index', $name),
            sprintf('%s.store', $name),
            sprintf('%s.show', $name),
            sprintf('%s.edit', $name),
            sprintf('%s.data', $name),
            sprintf('%s.create', $name),
            sprintf('%s.update', $name),
            sprintf('%s.destroy', $name),
            sprintf('%s.options', $name)
        ]);
    }


    public function getAction()
    {
        return (string) $this->action ?? '';
    }

    public function setAction($action): self
    {
        $this->action = trim($action, '/');
        return $this;
    }
}

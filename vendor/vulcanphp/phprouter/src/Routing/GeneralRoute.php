<?php

namespace VulcanPhp\PhpRouter\Routing;

use VulcanPhp\PhpRouter\Routing\IRoute;

class GeneralRoute extends IRoute
{
    /**
     * @param       $resource
     * @param array $config
     */
    public function __construct($path, array $config)
    {
        $this->setPath($path)
            ->setMethods($config['methods'])
            ->setName($config['name'] ?? '')
            ->setCallback($config['callback']);

        if (isset($config['regex'])) {
            $this->setRegex($config['regex']);
        } else {
            $this->generateRegex();
        }
    }

    public function generateRegex(): self
    {
        $this->regex = '%^' . preg_replace(['/{[^}]+\?}\//', '/{[^}]+}/', '/{*}/'], ['(.*?)[\/]?', '(.+)', '(.*)'], $this->getPath()) . '$%';
        return $this;
    }
}

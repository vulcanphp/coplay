<?php

namespace VulcanPhp\SweetView\Engine\Fire;

use VulcanPhp\SweetView\Engine\Html\Html;
use VulcanPhp\SweetView\Interfaces\IEngine;

class FireView implements IEngine
{
    protected Html $html;

    public function __construct(
        protected string $resourceDir,
        protected string $extension
    ) {
        $this->html = new Html();
        $this->html->resourceDir($resourceDir);
        $this->html->extension($extension);
    }

    public function isFireAgent(): bool
    {
        return strtolower($_SERVER['HTTP_CONTENT_AGENT'] ?? '') === 'fire-view';
    }

    public function render(array $params = []): string
    {
        if ($this->isFireAgent()) {
            return FireBlock::render($this->html, $params);
        }

        return $this->html->render($params);
    }

    public function getHtml(): Html
    {
        return $this->html;
    }

    public function getContent(string $path, array $params = []): string
    {
        return $this->html->getContent($path, $params);
    }

    public function __call($name, $arguments): self
    {
        call_user_func(
            [$this->html, $name],
            ...$arguments
        );

        return $this;
    }
}

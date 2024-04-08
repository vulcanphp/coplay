<?php

namespace VulcanPhp\SweetView\Engine\Fire;

use VulcanPhp\SweetView\Engine\Html\Html;

class FireBlock
{
    public function __construct(protected Html $html, protected array $params = [])
    {
    }

    public static function render(...$args): void
    {
        $block = new self(...$args);
        $block->responseInJson();
    }

    public function responseInJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            [
                'content'   => $this->getBlockContent(),
                'blocks'    => $this->getBlocks(),
                'title'     => $this->getDocumentTitle(),
            ],
            JSON_UNESCAPED_UNICODE
        );
        exit;
    }

    protected function getBlockContent(): string
    {
        return $this->html->getContent(
            $this->html->templateLocation(),
            $this->params
        );
    }

    protected function getDocumentTitle(): ?string
    {
        return $this->html->meta('title', null);
    }

    protected function getBlocks(): array
    {
        return $this->html->getBlocks();
    }
}

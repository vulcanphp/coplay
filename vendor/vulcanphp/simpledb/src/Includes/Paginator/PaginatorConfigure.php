<?php

namespace VulcanPhp\SimpleDb\Includes\Paginator;

trait PaginatorConfigure
{
    protected array $config = [
        'entity' => [
            'prev' => '&laquo;',
            'next' => '&raquo;',
        ],
        'style'  => [
            'ul' => 'pagination',
            'li' => 'page-item',
            'a'  => 'page-link',
            'li.current' => 'active',
            'a.current' => ''
        ],
        'action' => [
            'before'       => '',
            'before_items' => '',
            'after_items'  => '',
            'after'        => '',
        ],
    ];

    public function setEntity(string $key, $value): self
    {
        $this->config['entity'][$key] = $value;
        return $this;
    }

    public function getEntity(string $key): mixed
    {
        return $this->config['entity'][$key];
    }

    public function setStyle(string $key, $value): self
    {
        $this->config['style'][$key] = $value;
        return $this;
    }

    public function getStyle(string $key): mixed
    {
        return $this->config['style'][$key];
    }

    public function setAction(string $key, $value): self
    {
        $this->config['action'][$key] = $value;
        return $this;
    }

    public function getAction(string $key): mixed
    {
        return $this->config['action'][$key];
    }
}

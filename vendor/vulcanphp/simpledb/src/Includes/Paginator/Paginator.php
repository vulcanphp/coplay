<?php

namespace VulcanPhp\SimpleDb\Includes\Paginator;

class Paginator
{
    use PaginationManager, PaginatorConfigure;

    protected array $data = [];
    protected int $total, $limit;
    protected string $keyword;

    public function __construct(int $total, int $limit = 10, string $keyword = 'page')
    {
        $this->total = $total;
        $this->limit = $limit;
        $this->keyword = $keyword;

        $this->reset();
    }

    public static function create(...$args): Paginator
    {
        return new Paginator(...$args);
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasData(): bool
    {
        return !empty($this->data);
    }

    public function hasLinks(): bool
    {
        return $this->getPages() > 1;
    }

    public function getLinks(int $links = 2, string $paginator_class = ''): string
    {
        ob_start();

        $start = (($this->getPage() - $links) > 0) ? $this->getPage() - $links : 1;
        $end   = (($this->getPage() + $links) < $this->getPages()) ? $this->getPage() + $links : $this->getPages();

        echo $this->getAction('before');
        echo sprintf('<ul class="%s %s">', $this->getStyle('ul'), $paginator_class);
        echo $this->getAction('before_items');

        if ($this->getPage() > 1) {
            echo sprintf(
                '<li class="%s"><a class="%s" href="%s">%s</a></li>',
                $this->getStyle('li'),
                $this->getStyle('a'),
                $this->getAnchor($this->getPage() - 1),
                $this->getEntity('prev')
            );
        }

        if ($start > 1) {
            echo sprintf(
                '<li class="%s"><a class="%s" href="%s">%s</a></li>',
                $this->getStyle('li'),
                $this->getStyle('a'),
                $this->getAnchor(1),
                1
            );
            echo sprintf(
                '<li class="%s disabled"><span class="%s">...</span></li>',
                $this->getStyle('li'),
                $this->getStyle('a')
            );
        }

        for ($i = $start; $i <= $end; $i++) {
            echo sprintf(
                '<li class="%s %s"><a class="%s %s" href="%s">%s</a></li>',
                $this->getStyle('li'),
                $this->getPage() === $i ? $this->getStyle('li.current') : '',
                $this->getStyle('a'),
                $this->getPage() === $i ? $this->getStyle('a.current') : '',
                $this->getAnchor($i),
                $i
            );
        }

        if ($end < $this->getPages()) {
            echo sprintf(
                '<li class="%s disabled"><span class="%s">...</span></li>',
                $this->getStyle('li'),
                $this->getStyle('a')
            );

            echo sprintf(
                '<li class="%s"><a class="%s" href="%s">%s</a></li>',
                $this->getStyle('li'),
                $this->getStyle('a'),
                $this->getAnchor($this->getPages()),
                $this->getPages()
            );
        }

        if ($this->getPage() < $this->getPages()) {
            echo sprintf(
                '<li class="%s"><a class="%s" href="%s">%s</a></li>',
                $this->getStyle('li'),
                $this->getStyle('a'),
                $this->getAnchor($this->getPage() + 1),
                $this->getEntity('next')
            );
        }

        echo $this->getAction('after_items');
        echo '</ul>';
        echo $this->getAction('after');

        return ob_get_clean();
    }

    protected function getAnchor(int $page): string
    {
        $get = $_GET;

        $get[$this->getKeyword()] = $page;

        return '?' . http_build_query($get);
    }
}

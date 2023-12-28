<?php

namespace VulcanPhp\SimpleDb\Includes\Paginator;

trait PaginationManager
{
    protected $pages, $page, $offset;
    public function reset()
    {
        $this->pages  = ceil($this->getTotal() / $this->getLimit());
        $this->page   = min($this->getPages(), $this->getKeywordValue());
        $this->offset = ceil($this->getLimit() * ($this->getPage() - 1));
    }

    public function getPages(): int
    {
        return $this->pages > 0 ? $this->pages : 0;
    }

    public function getPage(): int
    {
        return $this->page > 0 ? $this->page : 0;
    }

    public function getOffset(): int
    {
        return $this->offset > 0 ? $this->offset : 0;
    }

    public function getTotal(): int
    {
        return $this->total > 0 ? $this->total : 0;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit > 0 ? $this->limit : 0;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function setKeyword(int $keyword): self
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function getKeywordValue(): int
    {
        return filter_input(
            INPUT_GET,
            $this->getKeyword(),
            FILTER_VALIDATE_INT,
            ['options' => ['default' => 1, 'min_range' => 1]]
        );
    }
}

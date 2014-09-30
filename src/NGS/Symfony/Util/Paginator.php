<?php
namespace NGS\Symfony\Util;

class Paginator
{
    private $count;
    private $page;
    private $perPage;
    private $pagesShown;

    function __construct($count, $page = 0, $perPage = 10, $pagesShown = 5)
    {
        $this->count = $count;
        $this->page = $page > 0 ? (int)$page : 1;
        $this->perPage = $perPage > 0 ? (int)$perPage : 10;
        $this->pagesShown = $pagesShown > 0 ? (int)$pagesShown : 5;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function setCount($count)
    {
        $this->count = $count > 0 ? (int)$count : 0;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function getTotalPages()
    {
        return ceil($this->count/$this->perPage);
    }

    public function getPages()
    {
        return range($this->getFirstPage(), $this->getLastPage());
    }

    public function getFirstPage()
    {
        return $this->page - floor($this->pagesShown/2) > 0 ? $this->page - floor($this->pagesShown/2) : 1;
    }

    public function getLastPage()
    {
        return $this->page+floor($this->pagesShown/2) <= $this->getTotalPages() ? $this->page+floor($this->pagesShown/2) : $this->getTotalPages();
    }

    public function getStart()
    {
        return ($this->page-1) * $this->perPage;
    }

    public function getOffset()
    {
        return ($this->page-1) * $this->perPage;
    }

    public function __toString()
    {
        return 'paginator';
    }
}

<?php

namespace App\Http\Integrations\StartGG;

trait HasPagination
{
    protected int $page = 1;

    protected int $perPage = 10;

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page)
    {
        $this->page = $page;

        return $this;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function setPerPage(int $perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }

}

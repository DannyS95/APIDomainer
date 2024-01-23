<?php

namespace App\Infrastructure\DTO;

final class ApiFiltersDTO
{
    private array $filters;
    private array $operations;
    private int $page;
    private int $itemsPerPage;

    public function __construct(array $filters, array $operations, int $page, int $itemsPerPage)
    {
        $this->filters = $filters;
        $this->operations = $operations;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOperations(): array
    {
        return $this->operations;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }
}

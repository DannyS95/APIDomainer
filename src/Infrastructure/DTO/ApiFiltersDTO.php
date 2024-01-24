<?php

namespace App\Infrastructure\DTO;

final class ApiFiltersDTO
{
    private ?array $filters;
    private ?array $operations;
    private ?int $page;
    private ?int $itemsPerPage;
    private ?array $sorts;

    public function __construct(?array $filters, ?array $operations, ?int $page, ?int $itemsPerPage, ?array $sorts)
    {
        $this->filters = $filters;
        $this->operations = $operations;
        $this->page = $page;
        $this->itemsPerPage = $itemsPerPage;
        $this->sorts = $sorts;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function getOperations(): ?array
    {
        return $this->operations;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    public function getSorts(): ?array
    {
        return $this->sorts;
    }
}

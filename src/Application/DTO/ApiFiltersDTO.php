<?php

namespace App\Application\DTO;

final class ApiFiltersDTO
{
    public function __construct(
        private array $filters,
        private array $operations,
        private array $sorts,
        private int $page,
        private int $itemsPerPage
    ) {}

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getOperations(): array
    {
        return $this->operations;
    }

    public function getSorts(): array
    {
        return $this->sorts;
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

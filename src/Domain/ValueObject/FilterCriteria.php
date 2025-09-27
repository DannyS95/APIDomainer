<?php

namespace App\Domain\ValueObject;

/**
 * Immutable filter criteria shared across repositories.
 *
 * @psalm-type FilterMap = array<string, mixed>
 * @psalm-type OperationMap = array<string, string>
 * @psalm-type SortMap = array<string, string>
 */
final class FilterCriteria
{
    /**
     * @param FilterMap $filters
     * @param OperationMap $operations
     * @param SortMap $sorts
     */
    public function __construct(
        private readonly array $filters,
        private readonly array $operations,
        private readonly array $sorts,
        private readonly int $page,
        private readonly int $itemsPerPage
    ) {
    }

    /**
     * @return FilterMap
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @return OperationMap
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * @return SortMap
     */
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

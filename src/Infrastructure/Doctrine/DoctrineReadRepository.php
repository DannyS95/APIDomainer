<?php

namespace App\Infrastructure\Doctrine;

use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Doctrine\QueryBuilder\DoctrineComparisonEnum;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class DoctrineReadRepository
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param array<string, string> $defaultSorts
     * @return array{0: int, 1: int, 2: array<string, string>}
     */
    protected function resolvePaginationAndSorts(
        FilterCriteria $filterCriteria,
        array $defaultSorts,
        int $defaultItemsPerPage,
        int $maxItemsPerPage
    ): array {
        $page = max(1, $filterCriteria->getPage());
        $itemsPerPage = $filterCriteria->getItemsPerPage() > 0
            ? $filterCriteria->getItemsPerPage()
            : $defaultItemsPerPage;
        $itemsPerPage = min($itemsPerPage, $maxItemsPerPage);
        $sorts = $filterCriteria->getSorts();

        if (empty($sorts)) {
            $sorts = $defaultSorts;
        }

        return [$page, $itemsPerPage, $sorts];
    }

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $operations
     */
    protected function applyFilters(
        QueryBuilder $queryBuilder,
        array $filters,
        array $operations,
        string $defaultAlias
    ): void {
        foreach ($filters as $field => $value) {
            $operator = $this->resolveOperator($operations[$field] ?? null);
            $parameter = $this->normalizeParameterName($field);
            [$alias, $fieldName] = $this->resolveAliasAndField($field, $defaultAlias);

            $queryBuilder
                ->andWhere(sprintf('%s.%s %s :%s', $alias, $fieldName, $operator, $parameter))
                ->setParameter($parameter, $this->prepareValue($operator, $value));
        }
    }

    /**
     * @param array<string, string> $sorts
     */
    protected function applySorts(QueryBuilder $queryBuilder, array $sorts, string $defaultAlias): void
    {
        foreach ($sorts as $field => $direction) {
            [$alias, $fieldName] = $this->resolveAliasAndField($field, $defaultAlias);
            $queryBuilder->addOrderBy(sprintf('%s.%s', $alias, $fieldName), strtoupper($direction));
        }
    }

    protected function paginate(QueryBuilder $queryBuilder, int $page, int $itemsPerPage): void
    {
        $queryBuilder
            ->setFirstResult(($page - 1) * $itemsPerPage)
            ->setMaxResults($itemsPerPage);
    }

    private function resolveOperator(?string $operation): string
    {
        if ($operation === null) {
            return DoctrineComparisonEnum::eq->value;
        }

        if (($enum = DoctrineComparisonEnum::tryFrom($operation)) !== null) {
            return $this->normalizeOperator($enum->value);
        }

        try {
            $value = DoctrineComparisonEnum::fromName($operation);
        } catch (\ValueError $exception) {
            throw new \InvalidArgumentException("Invalid operation: $operation", 0, $exception);
        }

        return $this->normalizeOperator($value);
    }

    private function normalizeOperator(string $operator): string
    {
        return match ($operator) {
            Comparison::CONTAINS => 'LIKE',
            default => $operator,
        };
    }

    private function prepareValue(string $operator, mixed $value): mixed
    {
        return match (strtoupper($operator)) {
            'LIKE' => sprintf('%%%s%%', $value),
            default => $value,
        };
    }

    private function normalizeParameterName(string $field): string
    {
        return str_replace('.', '_', $field);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function resolveAliasAndField(string $field, string $defaultAlias): array
    {
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $fieldName = array_pop($parts);
            $alias = array_pop($parts) ?? $defaultAlias;

            return [$alias, $fieldName];
        }

        return [$defaultAlias, $field];
    }
}

<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\DoctrineComparisonEnum;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use ValueError;

trait DoctrineComparisonFilterTrait
{
    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $operations
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $filters, array $operations, string $alias): void
    {
        foreach ($filters as $field => $value) {
            $operation = $this->normalizeOperation($operations[$field] ?? null);

            $queryBuilder
                ->andWhere(sprintf('%s.%s %s :%s', $alias, $field, $operation, $field))
                ->setParameter($field, $value);
        }
    }

    private function normalizeOperation(?string $operation): string
    {
        if ($operation === null) {
            return DoctrineComparisonEnum::eq->value;
        }

        $comparison = DoctrineComparisonEnum::tryFrom($operation);

        if ($comparison instanceof DoctrineComparisonEnum) {
            return $comparison->value;
        }

        try {
            return DoctrineComparisonEnum::fromName($operation);
        } catch (ValueError $exception) {
            throw new InvalidArgumentException(sprintf('Invalid operation: %s', $operation), 0, $exception);
        }
    }
}

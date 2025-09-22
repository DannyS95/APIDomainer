<?php

namespace App\Infrastructure\Repository;

use App\Infrastructure\DoctrineComparisonEnum;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractDoctrineQueryBuilder
{
    private ?QueryBuilder $qb = null;

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function create(): static
    {
        $builder = new static($this->entityManager);
        $builder->resetQueryBuilder();

        return $builder;
    }

    /**
     * @param array<string, mixed> $filters
     * @param array<string, string> $operations
     */
    public function whereClauses(array $filters, array $operations): static
    {
        $qb = $this->getQueryBuilder();

        foreach ($filters as $field => $value) {
            $operator = $this->resolveOperator($operations[$field] ?? null);
            $parameter = $this->normalizeParameterName($field);
            [$alias, $fieldName] = $this->resolveAliasAndField($field);

            $qb->andWhere(sprintf('%s.%s %s :%s', $alias, $fieldName, $operator, $parameter))
               ->setParameter($parameter, $this->prepareValue($operator, $value));
        }

        return $this;
    }

    /**
     * @param array<string, string> $sorts
     */
    public function addSorts(array $sorts): static
    {
        $qb = $this->getQueryBuilder();

        foreach ($sorts as $field => $direction) {
            [$alias, $fieldName] = $this->resolveAliasAndField($field);
            $qb->addOrderBy(sprintf('%s.%s', $alias, $fieldName), $direction);
        }

        return $this;
    }

    public function paginate(int $page, int $itemsPerPage): static
    {
        $qb = $this->getQueryBuilder();
        $qb->setFirstResult(($page - 1) * $itemsPerPage)
           ->setMaxResults($itemsPerPage);

        return $this;
    }

    protected function fetchArrayResult(): array
    {
        $qb = $this->getQueryBuilder();
        $results = $qb->getQuery()->getArrayResult();
        $this->clearQueryBuilder();

        return $results;
    }

    protected function fetchOneResult(): ?object
    {
        $qb = $this->getQueryBuilder();
        $result = $qb->getQuery()->getOneOrNullResult();
        $this->clearQueryBuilder();

        return $result;
    }

    protected function getQueryBuilder(): QueryBuilder
    {
        if ($this->qb === null) {
            $this->qb = $this->buildBaseQueryBuilder();
            $this->applyDefaultFilters($this->qb);
        }

        return $this->qb;
    }

    protected function resetQueryBuilder(): QueryBuilder
    {
        $this->clearQueryBuilder();

        return $this->getQueryBuilder();
    }

    protected function clearQueryBuilder(): void
    {
        $this->qb = null;
    }

    abstract protected function buildBaseQueryBuilder(): QueryBuilder;

    protected function applyDefaultFilters(QueryBuilder $qb): void
    {
        // Intentionally left blank for subclasses to override when needed.
    }

    abstract protected function alias(): string;

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
    private function resolveAliasAndField(string $field): array
    {
        if (str_contains($field, '.')) {
            $parts = explode('.', $field);
            $fieldName = array_pop($parts);
            $alias = array_pop($parts) ?? $this->alias();

            return [$alias, $fieldName];
        }

        return [$this->alias(), $field];
    }
}

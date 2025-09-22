<?php

declare(strict_types=1);

namespace Doctrine\ORM;

final class Query
{
    /**
     * @param array<int, array<string, mixed>|object> $results
     */
    public function __construct(private array $results, private ?string $entityClass = null)
    {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getArrayResult(): array
    {
        return array_map([$this, 'normalize'], $this->results);
    }

    /**
     * @return array<int, array<string, mixed>|object>
     */
    public function getResult(): array
    {
        return array_map(function (array|object $row): array|object {
            if (is_object($row)) {
                return $row;
            }

            if ($this->entityClass === null || !class_exists($this->entityClass)) {
                return (object) $row;
            }

            return $this->hydrateEntity($row);
        }, $this->results);
    }

    public function getOneOrNullResult(): ?object
    {
        if ($this->results === []) {
            return null;
        }

        $first = $this->results[0];

        if (is_object($first)) {
            return $first;
        }

        if ($this->entityClass === null || !class_exists($this->entityClass)) {
            return (object) $first;
        }

        return $this->hydrateEntity($first);
    }

    /**
     * @param array<string, mixed>|object $row
     * @return array<string, mixed>
     */
    private function normalize(array|object $row): array
    {
        if (is_array($row)) {
            return $row;
        }

        $data = [];
        $reflection = new \ReflectionObject($row);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $data[$property->getName()] = $property->getValue($row);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function hydrateEntity(array $row): object
    {
        $entityClass = $this->entityClass;
        $entity = new $entityClass();

        foreach ($row as $field => $value) {
            $method = 'set' . ucfirst($field);
            if (method_exists($entity, $method)) {
                $entity->$method($value);
                continue;
            }

            $this->setProperty($entity, $field, $value);
        }

        return $entity;
    }

    private function setProperty(object $entity, string $field, mixed $value): void
    {
        $reflection = new \ReflectionObject($entity);
        if (!$reflection->hasProperty($field)) {
            return;
        }

        $property = $reflection->getProperty($field);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
    }
}

interface EntityManagerInterface
{
    public function createQueryBuilder(): QueryBuilder;

    public function persist(object $entity): void;

    public function flush(): void;

    public function remove(object $entity): void;

    public function getRepository(string $className): object;
}

final class QueryBuilder
{
    private ?string $entityClass = null;

    private ?string $alias = null;

    /** @var array<string, mixed> */
    private array $parameters = [];

    /** @var array<int, array{field: string, operator: string, parameter: string}> */
    private array $conditions = [];

    /** @var array<int, array{field: string, direction: string}> */
    private array $orderBy = [];

    private ?int $firstResult = null;

    private ?int $maxResults = null;

    /** @var array<int, array<string, mixed>|object> */
    private array $data = [];

    /** @param array<class-string, array<int, array<string, mixed>>> $datasets */
    public function __construct(private array $datasets = [])
    {
    }

    public function select(string ...$select): self
    {
        return $this;
    }

    public function from(string $entityClass, string $alias): self
    {
        $this->entityClass = $entityClass;
        $this->alias = $alias;
        $this->data = $this->datasets[$entityClass] ?? [];

        return $this;
    }

    public function leftJoin(string $from, string $alias, ?string $conditionType = null, ?string $condition = null): self
    {
        return $this;
    }

    public function andWhere(string $condition): self
    {
        $pattern = '/^([a-zA-Z0-9_\.]+)\s*(=|!=|>=|<=|>|<|LIKE|IN)\s*:(\w+)$/';
        if (!preg_match($pattern, $condition, $matches)) {
            throw new \InvalidArgumentException(sprintf('Unsupported condition "%s".', $condition));
        }

        $this->conditions[] = [
            'field' => $matches[1],
            'operator' => strtoupper($matches[2]),
            'parameter' => $matches[3],
        ];

        return $this;
    }

    public function setParameter(string $name, mixed $value): self
    {
        $this->parameters[$name] = $value;

        return $this;
    }

    public function addOrderBy(string $field, string $direction): self
    {
        $this->orderBy[] = [
            'field' => $field,
            'direction' => strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC',
        ];

        return $this;
    }

    public function setFirstResult(int $firstResult): self
    {
        $this->firstResult = max(0, $firstResult);

        return $this;
    }

    public function setMaxResults(int $maxResults): self
    {
        $this->maxResults = $maxResults >= 0 ? $maxResults : null;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getRootAliases(): array
    {
        return $this->alias !== null ? [$this->alias] : [];
    }

    public function getQuery(): Query
    {
        $results = $this->applyConditions($this->data);
        $results = $this->applySorting($results);
        $results = $this->applyPagination($results);

        return new Query($results, $this->entityClass);
    }

    /**
     * @param array<int, array<string, mixed>> $dataset
     * @return array<int, array<string, mixed>>
     */
    private function applyConditions(array $dataset): array
    {
        if ($this->conditions === []) {
            return $dataset;
        }

        return array_values(array_filter(
            $dataset,
            function (array|object $row): bool {
                foreach ($this->conditions as $condition) {
                    $field = $this->extractField($condition['field']);
                    $value = $this->extractValue($row, $field);
                    $parameter = $this->parameters[$condition['parameter']] ?? null;

                    if (!$this->compare($value, $parameter, $condition['operator'])) {
                        return false;
                    }
                }

                return true;
            }
        ));
    }

    /**
     * @param array<int, array<string, mixed>> $dataset
     * @return array<int, array<string, mixed>>
     */
    private function applySorting(array $dataset): array
    {
        if ($this->orderBy === []) {
            return $dataset;
        }

        usort(
            $dataset,
            function (array|object $left, array|object $right): int {
                foreach ($this->orderBy as $order) {
                    $field = $this->extractField($order['field']);
                    $leftValue = $this->extractValue($left, $field);
                    $rightValue = $this->extractValue($right, $field);

                    if ($leftValue == $rightValue) {
                        continue;
                    }

                    $leftComparable = $this->normalizeComparable($leftValue);
                    $rightComparable = $this->normalizeComparable($rightValue);

                    if ($leftComparable === $rightComparable) {
                        continue;
                    }

                    $comparison = $leftComparable <=> $rightComparable;
                    if ($order['direction'] === 'DESC') {
                        $comparison *= -1;
                    }

                    if ($comparison !== 0) {
                        return $comparison;
                    }
                }

                return 0;
            }
        );

        return $dataset;
    }

    /**
     * @param array<string, mixed>|object $row
     */
    private function extractValue(array|object $row, string $field): mixed
    {
        if (is_array($row)) {
            return $row[$field] ?? null;
        }

        $getter = 'get' . ucfirst($field);
        if (method_exists($row, $getter)) {
            return $row->$getter();
        }

        $isser = 'is' . ucfirst($field);
        if (method_exists($row, $isser)) {
            return $row->$isser();
        }

        $reflection = new \ReflectionObject($row);
        if ($reflection->hasProperty($field)) {
            $property = $reflection->getProperty($field);
            $property->setAccessible(true);

            return $property->getValue($row);
        }

        return null;
    }

    private function normalizeComparable(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->getTimestamp();
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        if (is_object($value)) {
            return spl_object_hash($value);
        }

        return (string) $value;
    }

    /**
     * @param array<int, array<string, mixed>> $dataset
     * @return array<int, array<string, mixed>>
     */
    private function applyPagination(array $dataset): array
    {
        if ($this->firstResult === null && $this->maxResults === null) {
            return $dataset;
        }

        $offset = $this->firstResult ?? 0;
        $length = $this->maxResults ?? null;

        return array_slice($dataset, $offset, $length);
    }

    private function extractField(string $expression): string
    {
        $parts = explode('.', $expression);

        return (string) end($parts);
    }

    private function compare(mixed $fieldValue, mixed $parameterValue, string $operator): bool
    {
        $operator = strtoupper($operator);

        return match ($operator) {
            '=', 'EQ' => $fieldValue == $parameterValue,
            '!=', '<>' => $fieldValue != $parameterValue,
            '>', 'GT' => $fieldValue > $parameterValue,
            '>=', 'GTE' => $fieldValue >= $parameterValue,
            '<', 'LT' => $fieldValue < $parameterValue,
            '<=', 'LTE' => $fieldValue <= $parameterValue,
            'LIKE' => $this->compareLike($fieldValue, $parameterValue),
            'IN' => in_array($fieldValue, (array) $parameterValue, true),
            default => throw new \InvalidArgumentException(sprintf('Unsupported operator "%s".', $operator)),
        };
    }

    private function compareLike(mixed $fieldValue, mixed $parameterValue): bool
    {
        $needle = is_string($parameterValue) ? trim($parameterValue, '%') : '';

        return is_string($fieldValue) && str_contains(strtolower($fieldValue), strtolower($needle));
    }
}

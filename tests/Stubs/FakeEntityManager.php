<?php

declare(strict_types=1);

namespace Tests\Stub;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

final class FakeEntityManager implements EntityManagerInterface
{
    /**
     * @param array<class-string, array<int, array<string, mixed>>> $datasets
     */
    public function __construct(private array $datasets)
    {
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->datasets);
    }

    public function persist(object $entity): void
    {
    }

    public function flush(): void
    {
    }

    public function remove(object $entity): void
    {
    }

    public function getRepository(string $className): object
    {
        return new FakeObjectRepository($this->datasets[$className] ?? [], $className);
    }
}

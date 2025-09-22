<?php

declare(strict_types=1);

namespace Tests\Stub;

use App\Infrastructure\Repository\DoctrineRepositoryInterface;

final class FakeDoctrineRepository implements DoctrineRepositoryInterface
{
    public function createQueryBuilder(string $entityClass, string $entityAlias): self
    {
        return $this;
    }

    public function whereEqual(string $field, mixed $value): self
    {
        return $this;
    }

    public function whereLike(string $field, mixed $value): self
    {
        return $this;
    }

    public function buildSorts(array $sorts): self
    {
        return $this;
    }

    public function buildPagination(int $page, int $itemsPerPage): self
    {
        return $this;
    }

    public function fetchArray(): array
    {
        return [];
    }

    public function findOne(): ?object
    {
        return null;
    }

    public function persist(object $entity): void
    {
    }

    public function save(): void
    {
    }

    public function remove(object $entity): void
    {
    }
}

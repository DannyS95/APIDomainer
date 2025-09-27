<?php

namespace App\Infrastructure\Doctrine\Repository;

interface DoctrineRepositoryInterface
{
    public function createQueryBuilder(string $entityClass, string $entityAlias): self;

    public function whereEqual(string $field, mixed $value): self;

    public function whereLike(string $field, mixed $value): self;

    public function buildSorts(array $sorts): self;

    public function buildPagination(int $page, int $itemsPerPage): self;

    public function fetchArray(): array;

    public function findOne(): ?object;

    public function persist(object $entity): void;

    public function save(): void;

    public function remove(object $entity): void;
}

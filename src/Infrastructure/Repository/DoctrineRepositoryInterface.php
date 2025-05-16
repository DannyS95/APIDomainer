<?php

namespace App\Infrastructure\Repository;

interface DoctrineRepositoryInterface
{
    public function createQueryBuilder(string $entityClass, string $entityAlias): self;

    public function buildClauses(?array $filters, ?array $operations): self;
    
    public function buildSorts(?array $sorts): self;
    
    public function buildPagination(?int $page, ?int $itemsPerPage): self;

    public function fetchArray(): array;

    public function persist(object $entity): void;

    public function save(): void;

    public function serviceRepo(): ?object;
}

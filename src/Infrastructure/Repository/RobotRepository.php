<?php

namespace App\Infrastructure\Repository;

use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Repository\DoctrineRepository;

final class RobotRepository extends DoctrineRepository implements RobotRepositoryInterface
{
    public function findAll(?int $page, ?int $itemsPerPage, ?array $filters, ?array $operations)
    {
       $this->create(page: $page, itemsPerPage: $itemsPerPage, filters: $filters, operations: $operations);
    }
}

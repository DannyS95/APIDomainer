<?php

namespace App\Domain\Repository;

interface RobotRepositoryInterface
{
    public function findAll(?int $page, ?int $itemsPerPage, ?array $filters, array $operations);
}

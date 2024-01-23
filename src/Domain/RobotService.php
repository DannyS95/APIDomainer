<?php

namespace App\Domain;

use App\Domain\Repository\RobotRepositoryInterface;

final class RobotService
{
    public function __construct(private RobotRepositoryInterface $robotRepository)
    {
    }

    /**
     * Find all Robot Resources agains't given filters and operations.
     *
     * @param array<string, string> $filters
     * @param array<string, string> $operations
     * @return void
     */
    public function getRobots(?int $page, ?int $itemsPerPage, ?array $filters, ?array $operations)
    {
        $this->robotRepository->findAll(page: $page, itemsPerPage: $itemsPerPage, filters: $filters, operations: $operations);
    }
}

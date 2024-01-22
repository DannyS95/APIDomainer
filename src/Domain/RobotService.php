<?php

namespace App\Domain;

class RobotService
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
    public function getRobots(array $filters, array $operations)
    {
        dd($filters);
    }
}

<?php

namespace App\Domain;

use App\Infrastructure\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;

final class RobotService
{
    public function __construct(private RobotRepositoryInterface $robotRepository)
    {
    }

    /**
     * Find all Robot Resources agains't given API Filters.
     *
     * @param ApiFiltersDTO $filters
     * @return void
     */
    public function getRobots(ApiFiltersDTO $apiFiltersDTO)
    {
        $robots = $this->robotRepository->findAll($apiFiltersDTO);
    }
}

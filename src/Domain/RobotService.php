<?php

namespace App\Domain;

use App\Infrastructure\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Infrastructure\Request\RobotDanceOffRequest;

final class RobotService
{
    public function __construct(private RobotRepositoryInterface $robotRepository)
    {
    }

    /**
     * Find all Robot Resources agains't given API Filters.
     *
     * @param ApiFiltersDTO $filters
     * @return array|null
     */
    public function getRobots(ApiFiltersDTO $apiFiltersDTO)
    {
        return $this->robotRepository->findAll($apiFiltersDTO);
    }

    /**
     * Find all Robot Resources agains't given API Filters.
     *
     * @param RobotDanceOffRequest $filters
     * @return array|null
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest)
    {
        dd($robotDanceOffRequest);
    }
}

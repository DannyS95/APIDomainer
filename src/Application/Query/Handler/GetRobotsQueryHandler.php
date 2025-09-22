<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotsQuery;
use App\Domain\Repository\RobotRepositoryInterface;

final class GetRobotsQueryHandler
{
    public function __construct(private readonly RobotRepositoryInterface $robotRepository)
    {
    }

    public function __invoke(GetRobotsQuery $query): array
    {
        return $this->robotRepository->findAll($query->getApiFiltersDTO());
    }
}

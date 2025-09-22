<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotDanceOffsQuery;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;

final class GetRobotDanceOffsQueryHandler
{
    public function __construct(private readonly RobotDanceOffRepositoryInterface $robotDanceOffRepository)
    {
    }

    public function __invoke(GetRobotDanceOffsQuery $query): array
    {
        return $this->robotDanceOffRepository->findAll($query->getApiFiltersDTO());
    }
}

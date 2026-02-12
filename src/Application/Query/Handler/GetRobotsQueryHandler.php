<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotsQuery;
use App\Domain\Repository\RobotReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotsQueryHandler
{
    public function __construct(private readonly RobotReadRepositoryInterface $robotReadRepository)
    {
    }

    public function __invoke(GetRobotsQuery $query): array
    {
        return $this->robotReadRepository->findByCriteria(
            $query->getApiFiltersDTO()->toFilterCriteria()
        );
    }
}

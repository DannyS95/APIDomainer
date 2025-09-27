<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotDanceOffQuery;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotDanceOffQueryHandler
{
    public function __construct(private readonly RobotDanceOffRepositoryInterface $robotDanceOffRepository)
    {
    }

    public function __invoke(GetRobotDanceOffQuery $query): array
    {
        return $this->robotDanceOffRepository->findAll(
            $query->getApiFiltersDTO()->toFilterCriteria()
        );
    }
}

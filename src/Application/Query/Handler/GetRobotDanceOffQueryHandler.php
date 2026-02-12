<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotDanceOffQuery;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotBattleViewReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotDanceOffQueryHandler
{
    public function __construct(private readonly RobotBattleViewReadRepositoryInterface $robotBattleViewReadRepository)
    {
    }

    /**
     * @return list<RobotBattleViewInterface>
     */
    public function __invoke(GetRobotDanceOffQuery $query): array
    {
        return $this->robotBattleViewReadRepository->findByCriteria(
            $query->getApiFiltersDTO()->toFilterCriteria()
        );
    }
}

<?php

namespace App\Application\Query\Handler;

use App\Application\Query\GetRobotDanceOffTeamsQuery;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotBattleViewReadRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetRobotDanceOffTeamsQueryHandler
{
    public function __construct(
        private readonly RobotBattleViewReadRepositoryInterface $robotBattleViewReadRepository
    ) {
    }

    /**
     * @return list<RobotBattleViewInterface>
     */
    public function __invoke(GetRobotDanceOffTeamsQuery $query): array
    {
        $criteria = new FilterCriteria(
            ['battleId' => $query->battleId()],
            ['battleId' => 'eq'],
            ['createdAt' => 'DESC'],
            1,
            50
        );

        return $this->robotBattleViewReadRepository->findByCriteria($criteria);
    }
}

<?php

namespace App\Action;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\ValueObject\FilterCriteria;
use App\Infrastructure\Response\RobotBattleTeamsResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotBattleTeamsAction
{
    public function __construct(private RobotDanceOffRepositoryInterface $robotDanceOffRepository)
    {
    }

    /**
     * @return array<int, RobotBattleTeamsResponse>
     */
    public function __invoke(Request $request): array
    {
        $battleId = (int) $request->attributes->get('battleId', 0);

        $filterCriteria = new FilterCriteria(
            ['battleId' => $battleId],
            ['battleId' => 'eq'],
            ['createdAt' => 'DESC'],
            1,
            50
        );

        $danceOffs = $this->robotDanceOffRepository->findAll($filterCriteria);

        return array_map(
            static fn (RobotBattleViewInterface $danceOff): RobotBattleTeamsResponse => new RobotBattleTeamsResponse(
                $danceOff->getBattleId(),
                $danceOff->getId(),
                $danceOff->getTeamOneRobotIds(),
                $danceOff->getTeamTwoRobotIds()
            ),
            $danceOffs
        );
    }
}

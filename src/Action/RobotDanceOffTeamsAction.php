<?php

namespace App\Action;

use App\Application\Query\GetRobotDanceOffTeamsQuery;
use App\Application\Query\QueryBusDispatcher;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Infrastructure\Response\RobotDanceOffTeamsResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
final class RobotDanceOffTeamsAction
{
    public function __construct(private QueryBusDispatcher $queryBusDispatcher)
    {
    }

    /**
     * @return array<int, RobotDanceOffTeamsResponse>
     */
    public function __invoke(Request $request): array
    {
        $battleId = (int) $request->attributes->get('battleId', 0);

        if ($battleId <= 0) {
            throw new BadRequestHttpException('Battle ID must be a positive integer.');
        }

        $query = new GetRobotDanceOffTeamsQuery($battleId);
        $danceOffs = $this->queryBusDispatcher->askArray($query);

        return array_map(
            static fn (RobotBattleViewInterface $danceOff): RobotDanceOffTeamsResponse => new RobotDanceOffTeamsResponse(
                $danceOff->getBattleId(),
                $danceOff->getBattleReplayId(),
                $danceOff->getTeamOneRobotIds(),
                $danceOff->getTeamTwoRobotIds()
            ),
            $danceOffs
        );
    }
}

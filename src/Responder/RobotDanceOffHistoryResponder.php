<?php

namespace App\Responder;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Infrastructure\Response\RobotDanceOffResponse;

/**
 * Shapes history responses for robot dance-offs tied to a battle.
 */
final class RobotDanceOffHistoryResponder
{
    /**
     * @param list<RobotBattleViewInterface> $danceOffs
     * @return list<RobotDanceOffResponse>
     */
    public function respond(array $danceOffs): array
    {
        return array_map([$this, 'assemble'], $danceOffs);
    }

    private function assemble(RobotBattleViewInterface $danceOff): RobotDanceOffResponse
    {
        return new RobotDanceOffResponse(
            $danceOff->getBattleReplayId(),
            $danceOff->getBattleId(),
            $danceOff->getTeamOne(),
            $danceOff->getTeamTwo(),
            $danceOff->getWinningTeam(),
            $danceOff->getTeamOnePower(),
            $danceOff->getTeamTwoPower()
        );
    }
}

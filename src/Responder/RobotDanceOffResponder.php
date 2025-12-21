<?php

namespace App\Responder;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Infrastructure\Response\RobotDanceOffResponse;

final class RobotDanceOffResponder
{
    /**
     * Transform and respond with a collection of JSON payloads.
     *
     * @param RobotBattleViewInterface[] $danceOffs
     * @return array<int, RobotDanceOffResponse>
     */
    public function respond(array $danceOffs): array
    {
        return array_map([$this, 'assemble'], $danceOffs);
    }

    /**
     * Transform a single RobotDanceOff into a RobotDanceOffResponse.
     */
    private function assemble(RobotBattleViewInterface $danceOff): RobotDanceOffResponse
    {
        return new RobotDanceOffResponse(
            $danceOff->getBattleReplayId(),
            $danceOff->getBattleId(),
            $danceOff->getOriginBattleId(),
            $danceOff->getTeamOne(),
            $danceOff->getTeamTwo(),
            $danceOff->getWinningTeam(),
            $danceOff->getTeamOnePower(),
            $danceOff->getTeamTwoPower()
        );
    }
}

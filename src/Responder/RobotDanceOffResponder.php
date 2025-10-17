<?php

namespace App\Responder;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Infrastructure\Response\RobotDanceOffResponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class RobotDanceOffResponder
{
    /**
     * Transform and respond with a collection of JSON payloads.
     *
     * @param RobotBattleViewInterface[] $danceOffs
     * @return Collection
     */
    public function respond(array $danceOffs): Collection
    {
        $mappedResponses = array_map([$this, 'assemble'], $danceOffs);

        return new ArrayCollection($mappedResponses);
    }

    /**
     * Transform a single RobotDanceOff into a RobotDanceOffResponse.
     */
    private function assemble(RobotBattleViewInterface $danceOff): RobotDanceOffResponse
    {
        return new RobotDanceOffResponse(
            $danceOff->getId(),
            $danceOff->getBattleId(),
            $danceOff->getTeamOne(),
            $danceOff->getTeamTwo(),
            $danceOff->getWinningTeam()
        );
    }
}

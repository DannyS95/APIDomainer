<?php

namespace App\Responder;

use App\Domain\Entity\Team;
use App\Domain\Entity\RobotDanceOff;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Infrastructure\Response\RobotDanceOffResponse;

final class RobotDanceOffResponder
{
    /**
     * Transform and respond with a collection of JSON payloads.
     *
     * @param RobotDanceOff[] $danceOffs
     * @return Collection
     */
    public function respond(array $danceOffs): Collection
    {
        $mappedResponses = array_map([$this, 'assemble'], $danceOffs);

        return new ArrayCollection($mappedResponses);
    }

    /**
     * Transform a single RobotDanceOff into a RobotDanceOffResponse.
     *
     * @param RobotDanceOff $danceOff
     * @return RobotDanceOffResponse
     */
    private function assemble(RobotDanceOff $danceOff): RobotDanceOffResponse
    {
        $teamOneDetails = $this->mapTeamDetails($danceOff->getTeamOne());
        $teamTwoDetails = $this->mapTeamDetails($danceOff->getTeamTwo());

        $winningTeamDetails = $danceOff->getWinningTeam()
            ? $this->mapTeamDetails($danceOff->getWinningTeam())
            : null;

        return new RobotDanceOffResponse(
            $danceOff->getId(),
            $teamOneDetails,
            $teamTwoDetails,
            $winningTeamDetails
        );
    }

    /**
     * Map the details of a team and its robots.
     *
     * @param Team $team
     * @return array
     */
    private function mapTeamDetails(Team $team): array
    {
        return [
            'id' => $team->getId(),
            'name' => $team->getName(),
            'robots' => $team->getRobots()->map(fn($robot) => [
                'id' => $robot->getId(),
                'name' => $robot->getName(),
                'powermove' => $robot->getPowermove(),
                'experience' => $robot->getExperience(),
                'outOfOrder' => $robot->isOutOfOrder(),
                'avatar' => $robot->getAvatar()
            ])->toArray()
        ];
    }
}

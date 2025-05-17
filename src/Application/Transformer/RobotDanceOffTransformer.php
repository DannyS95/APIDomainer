<?php

namespace App\Application\Transformer;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Infrastructure\Response\RobotDanceOffResponse;

final class RobotDanceOffTransformer
{
    public function transform(RobotDanceOff $danceOff): RobotDanceOffResponse
    {
        // Map the two teams into the response format
        $teamOneDetails = $this->mapTeamDetails($danceOff->getTeamOne());
        $teamTwoDetails = $this->mapTeamDetails($danceOff->getTeamTwo());

        // Map the winning team details if it exists
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

<?php

namespace App\Domain\Service;

use RobotServiceException;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Infrastructure\Request\RobotDanceOffRequest;

final class RobotService
{
    public function __construct(
        private RobotRepositoryInterface $robotRepository,
        private RobotDanceOffRepositoryInterface $robotDanceOffRepository,
        private TeamRepositoryInterface $teamRepository,
        private RobotValidatorService $robotValidatorService
    ) {}

    /**
     * Set a dance off between two teams of robots.
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest): void
    {
        $robotIds = [
            ...$robotDanceOffRequest->teamA,
            ...$robotDanceOffRequest->teamB,
        ];

        $this->robotValidatorService->validateRobotIds($robotIds);

        // Create the teams and associate robots
        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        foreach ($robotDanceOffRequest->teamA as $robotId) {
            $teamOne->addRobot($this->loadRobot($robotId));
        }

        foreach ($robotDanceOffRequest->teamB as $robotId) {
            $teamTwo->addRobot($this->loadRobot($robotId));
        }

        // Persist teams
        $this->teamRepository->save($teamOne);
        $this->teamRepository->save($teamTwo);

        // Create the DanceOff and set relationships
        $danceOff = new RobotDanceOff();
        $danceOff->setTeamOne($teamOne);
        $danceOff->setTeamTwo($teamTwo);

        // Calculate and set the winning team
        $winningTeam = $this->calculateWinningTeam($teamOne, $teamTwo);
        $danceOff->setWinningTeam($winningTeam);

        // Persist the DanceOff
        $this->robotDanceOffRepository->save($danceOff);
    }

    /**
     * Determines the winning team based on individual battles.
     */
    private function calculateWinningTeam(Team $teamOne, Team $teamTwo): ?Team
    {
        $teamOneWins = 0;
        $teamTwoWins = 0;

        foreach (range(0, 4) as $index) {
            $robotA = $teamOne->getRobots()->get($index);
            $robotB = $teamTwo->getRobots()->get($index);

            if ($robotA->getExperience() > $robotB->getExperience()) {
                $teamOneWins++;
            } elseif ($robotB->getExperience() > $robotA->getExperience()) {
                $teamTwoWins++;
            }
        }

        if ($teamOneWins > $teamTwoWins) {
            return $teamOne;
        } elseif ($teamTwoWins > $teamOneWins) {
            return $teamTwo;
        }

        return null;
    }

    private function loadRobot(int $id): Robot
    {
        $robot = $this->robotRepository->findOneBy($id);

        if ($robot === null) {
            throw new RobotServiceException("Robot ID $id does not exist.");
        }

        return $robot;
    }
}

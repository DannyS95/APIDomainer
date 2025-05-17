<?php

namespace App\Domain\Service;

use App\Application\Transformer\RobotDanceOffTransformer;
use RobotServiceException;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\Team;
use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Domain\Repository\TeamRepositoryInterface;
use App\Infrastructure\Request\RobotDanceOffRequest;
use App\Infrastructure\Response\RobotDanceOffResponse;

final class RobotService
{
    public function __construct(
        private RobotRepositoryInterface $robotRepository,
        private RobotDanceOffRepositoryInterface $robotDanceOffRepository,
        private TeamRepositoryInterface $teamRepository,
        private RobotValidatorService $robotValidatorService
    ) {}

    /**
     * Find all Robot Resources against given API Filters.
     *
     * @param ApiFiltersDTO $apiFiltersDTO
     * @return array|null
     */
    public function getRobots(ApiFiltersDTO $apiFiltersDTO): array
    {
        return $this->robotRepository->findAll($apiFiltersDTO);
    }

    /**
     * Find all Robot DanceOffs with their full team details.
     */
    public function getRobotDanceOffs(ApiFiltersDTO $apiFiltersDTO): array
    {
        return $this->robotDanceOffRepository->findAll($apiFiltersDTO);
    }

    /**
     * Find a robot by ID.
     *
     * @param int $id The ID of the robot to find.
     * 
     * @return Robot The found Robot entity.
     * 
     * @throws RobotServiceException If the robot with the given ID does not exist.
     */
    public function getRobot(int $id): Robot
    {
        $this->robotValidatorService->validateRobotIds([$id]);

        return $this->robotRepository->findOneBy($id);
    }

    /**
     * Set a dance off between two teams of robots.
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest): void
    {
        // Create the teams and associate robots
        $teamOne = new Team('Team One');
        $teamTwo = new Team('Team Two');

        foreach ($robotDanceOffRequest->teamA as $robotId) {
            $robot = $this->getRobot($robotId);
            $teamOne->addRobot($robot);
        }

        foreach ($robotDanceOffRequest->teamB as $robotId) {
            $robot = $this->getRobot($robotId);
            $teamTwo->addRobot($robot);
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
}

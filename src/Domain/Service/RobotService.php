<?php

namespace App\Domain\Service;

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
        private TeamRepositoryInterface $teamRepository
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
        $danceOffs = $this->robotDanceOffRepository->findAll($apiFiltersDTO);

        return array_map(function (RobotDanceOff $danceOff) {
            return new RobotDanceOffResponse(
                $danceOff->getId(),
                $this->mapTeamDetails($danceOff->getTeamOne()),
                $this->mapTeamDetails($danceOff->getTeamTwo()),
                $danceOff->getWinner() ? $this->mapTeamDetails($danceOff->getWinner()) : null
            );
        }, $danceOffs);
    }

    /**
     * Map team details to a structured array.
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

    /**
     * Find a robot by ID.
     *
     * @param int $id
     * @return Robot
     */
    public function getRobot(int $id): Robot
    {
        $entity = $this->robotRepository->findOneBy($id);

        if ($entity === null) {
            throw new RobotServiceException("There are no robots with id: $id", 404);
        }

        return $entity;
    }

    /**
     * Set a dance off between two teams of robots.
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest): void
    {
        // Validation
        if (count($robotDanceOffRequest->teamA) !== 5 || count($robotDanceOffRequest->teamB) !== 5) {
            throw new RobotServiceException("Each team must have exactly 5 robots.", 400);
        }

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

        // Calculate and set the winner
        $winner = $this->calculateWinningTeam($teamOne, $teamTwo);
        $danceOff->setWinner($winner);

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

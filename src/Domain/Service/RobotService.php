<?php

namespace App\Domain\Service;

use RobotServiceException;
use App\Domain\Entity\Robot;
use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\RobotDanceOffParticipant;
use App\Application\DTO\ApiFiltersDTO;
use App\Domain\Repository\RobotRepositoryInterface;
use App\Domain\Repository\RobotDanceOffRepositoryInterface;
use App\Infrastructure\Request\RobotDanceOffRequest;

final class RobotService
{
    public function __construct(
        private RobotRepositoryInterface $robotRepository,
        private RobotDanceOffRepositoryInterface $robotDanceOffRepository,
    ) {
    }

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
     * Get all Robot Dance Offs based on the filters.
     */
    public function getRobotDanceOffs(ApiFiltersDTO $apiFiltersDTO): array
    {
        $danceOffs = $this->robotDanceOffRepository->findAll($apiFiltersDTO);

        return array_map(function (RobotDanceOff $danceOff) {
            return [
                'id' => $danceOff->getId(),
                'createdAt' => $danceOff->getCreatedAt()->format('Y-m-d H:i:s'),
                'teamOne' => $this->formatTeam($danceOff->getTeamOne()),
                'teamTwo' => $this->formatTeam($danceOff->getTeamTwo()),
                'winner' => $danceOff->getWinner()?->getId()
            ];
        }, $danceOffs);
    }

    /**
     * Format the team for JSON response.
     */
    private function formatTeam($team): array
    {
        return array_map(function ($participant) {
            $robot = $participant->getRobot();
            return [
                'id' => $robot->getId(),
                'name' => $robot->getName(),
                'powermove' => $robot->getPowermove(),
                'experience' => $robot->getExperience(),
                'outOfOrder' => $robot->isOutOfOrder(),
                'avatar' => $robot->getAvatar()
            ];
        }, $team->toArray());
    }


    /**
     * Find a robot by ID.
     *
     * @param int $id
     * @return Robot|\Exception
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
     *
     * @param RobotDanceOffRequest $robotDanceOffRequest
     * @return void
     */
    public function setRobotDanceOff(RobotDanceOffRequest $robotDanceOffRequest): void
    {
        $this->validateTeams($robotDanceOffRequest);

        $teamOne = array_map(fn($id) => $this->getRobot($id), $robotDanceOffRequest->teamA);
        $teamTwo = array_map(fn($id) => $this->getRobot($id), $robotDanceOffRequest->teamB);

        $danceOff = new RobotDanceOff();
        $this->robotDanceOffRepository->bulkSave([$danceOff]);

        $participants = array_merge(
            $this->createParticipants($teamOne, $danceOff, 'teamOne'),
            $this->createParticipants($teamTwo, $danceOff, 'teamTwo')
        );

        $this->robotDanceOffRepository->bulkSave($participants);

        $winner = $this->calculateWinningTeam($teamOne, $teamTwo);
    $danceOff->setWinner($winner);

    $this->robotDanceOffRepository->bulkSave([$danceOff]);
    }

    /**
     * Validate that no robot exists in both teams.
     */
    private function validateTeams(RobotDanceOffRequest $robotDanceOffRequest): void
    {
    foreach ($robotDanceOffRequest->teamA as $robotId) {
        if (\in_array($robotId, $robotDanceOffRequest->teamB, true)) {
            throw new RobotServiceException("Robot $robotId was selected for both teams. Please choose another.", 403);
        }
    }
    }

    /**
     * Create participants for a specific team.
     */
    private function createParticipants(array $team, RobotDanceOff $danceOff, string $teamName): array
    {
        return array_map(function (Robot $robot) use ($danceOff, $teamName) {
            $participant = new RobotDanceOffParticipant();
            $participant->setDanceOff($danceOff);
            $participant->setRobot($robot);
            $participant->setTeam($teamName);
            return $participant;
        }, $team);
    }

    /**
     * Determines the winning team based on the experience sum.
     *
     * @param array<Robot> $teamOne
     * @param array<Robot> $teamTwo
     * @return Robot|null
     */
    private function calculateWinningTeam(array $teamOne, array $teamTwo): ?Robot
    {
        $teamOneExperience = array_sum(array_map(fn(Robot $r) => $r->getExperience(), $teamOne));
        $teamTwoExperience = array_sum(array_map(fn(Robot $r) => $r->getExperience(), $teamTwo));

        if ($teamOneExperience === $teamTwoExperience) {
            return null; // It's a tie, no winner
        }

        return $teamOneExperience > $teamTwoExperience ? $teamOne[0] : $teamTwo[0];
    }

}

<?php

namespace App\Infrastructure\Response;

final class RobotBattleTeamsResponse
{
    /**
     * @param array<int, int> $teamOneRobotIds
     * @param array<int, int> $teamTwoRobotIds
     */
    public function __construct(
        private int $battleId,
        private int $danceOffId,
        private array $teamOneRobotIds,
        private array $teamTwoRobotIds
    ) {
    }

    public function getBattleId(): int
    {
        return $this->battleId;
    }

    public function getDanceOffId(): int
    {
        return $this->danceOffId;
    }

    /**
     * @return array<int, int>
     */
    public function getTeamOneRobotIds(): array
    {
        return $this->teamOneRobotIds;
    }

    /**
     * @return array<int, int>
     */
    public function getTeamTwoRobotIds(): array
    {
        return $this->teamTwoRobotIds;
    }
}

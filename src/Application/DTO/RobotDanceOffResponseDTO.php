<?php

namespace App\Infrastructure\Responder\DTO;

class RobotDanceOffResponseDTO
{
    private int $battleReplayId;
    private array $teamOne;
    private array $teamTwo;
    private ?int $winningTeam;

    public function __construct(
        int $battleReplayId,
        array $teamOne,
        array $teamTwo,
        ?int $winningTeam = null
    ) {
        $this->battleReplayId = $battleReplayId;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winningTeam = $winningTeam;
    }

    public function getBattleReplayId(): int
    {
        return $this->battleReplayId;
    }

    public function getTeamOne(): array
    {
        return $this->teamOne;
    }

    public function getTeamTwo(): array
    {
        return $this->teamTwo;
    }

    public function getWinningTeam(): ?int
    {
        return $this->winningTeam;
    }
}

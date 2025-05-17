<?php

namespace App\Infrastructure\Responder\DTO;

class RobotDanceOffResponseDTO
{
    private int $id;
    private array $teamOne;
    private array $teamTwo;
    private ?int $winningTeam;

    public function __construct(
        int $id,
        array $teamOne,
        array $teamTwo,
        ?int $winningTeam = null
    ) {
        $this->id = $id;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winningTeam = $winningTeam;
    }

    public function getId(): int
    {
        return $this->id;
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

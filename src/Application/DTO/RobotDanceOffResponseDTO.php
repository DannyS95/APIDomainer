<?php

namespace App\Infrastructure\Responder\DTO;

class RobotDanceOffResponseDTO
{
    private int $id;
    private array $teamOne;
    private array $teamTwo;
    private ?int $winner;

    public function __construct(
        int $id,
        array $teamOne,
        array $teamTwo,
        ?int $winner = null
    ) {
        $this->id = $id;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winner = $winner;
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

    public function getWinner(): ?int
    {
        return $this->winner;
    }
}

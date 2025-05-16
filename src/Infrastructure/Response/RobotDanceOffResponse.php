<?php

namespace App\Infrastructure\Response;

class RobotDanceOffResponse
{
    private int $id;
    private array $teamOne;
    private array $teamTwo;
    private ?int $winnerId;

    public function __construct(
        int $id,
        array $teamOne,
        array $teamTwo,
        ?int $winnerId
    ) {
        $this->id = $id;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winnerId = $winnerId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array<int> List of IDs representing Team One
     */
    public function getTeamOne(): array
    {
        return $this->teamOne;
    }

    /**
     * @return array<int> List of IDs representing Team Two
     */
    public function getTeamTwo(): array
    {
        return $this->teamTwo;
    }

    public function getWinnerId(): ?int
    {
        return $this->winnerId;
    }
}

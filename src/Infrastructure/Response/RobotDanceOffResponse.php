<?php

namespace App\Infrastructure\Response;

class RobotDanceOffResponse
{
    private int $id;
    private array $teamOne;
    private array $teamTwo;
    private ?array $winner;

    public function __construct(
        int $id,
        array $teamOne,
        array $teamTwo,
        ?array $winner
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

    /**
     * @return array<array> List of Robot data for Team One
     */
    public function getTeamOne(): array
    {
        return $this->teamOne;
    }

    /**
     * @return array<array> List of Robot data for Team Two
     */
    public function getTeamTwo(): array
    {
        return $this->teamTwo;
    }

    /**
     * @return array|null Full data of the winning team, if any
     */
    public function getWinner(): ?array
    {
        return $this->winner;
    }
}

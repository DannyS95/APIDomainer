<?php

namespace App\Infrastructure\Response;

class RobotDanceOffResponse
{
    private int $id;
    private array $teamOne;
    private array $teamTwo;
    private ?array $winningTeam;

    public function __construct(
        int $id,
        array $teamOne,
        array $teamTwo,
        ?array $winningTeam
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
    public function getWinningTeam(): ?array
    {
        return $this->winningTeam;
    }
}

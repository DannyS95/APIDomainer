<?php

namespace App\Infrastructure\Response;

final class RobotDanceOffResponse
{
    private int $id;
    private int $battleId;
    private array $teamOne;
    private array $teamTwo;
    private ?array $winningTeam;

    public function __construct(
        int $id,
        int $battleId,
        array $teamOne,
        array $teamTwo,
        ?array $winningTeam
    ) {
        $this->id = $id;
        $this->battleId = $battleId;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winningTeam = $winningTeam;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getBattleId(): int
    {
        return $this->battleId;
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

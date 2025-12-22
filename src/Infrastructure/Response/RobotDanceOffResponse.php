<?php

namespace App\Infrastructure\Response;

final class RobotDanceOffResponse
{
    private int $battleReplayId;
    private int $battleId;
    private array $teamOne;
    private array $teamTwo;
    private ?array $winningTeam;
    private int $teamOnePower;
    private int $teamTwoPower;

    public function __construct(
        int $battleReplayId,
        int $battleId,
        array $teamOne,
        array $teamTwo,
        ?array $winningTeam,
        int $teamOnePower,
        int $teamTwoPower
    ) {
        $this->battleReplayId = $battleReplayId;
        $this->battleId = $battleId;
        $this->teamOne = $teamOne;
        $this->teamTwo = $teamTwo;
        $this->winningTeam = $winningTeam;
        $this->teamOnePower = $teamOnePower;
        $this->teamTwoPower = $teamTwoPower;
    }

    public function getBattleReplayId(): int
    {
        return $this->battleReplayId;
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

    public function getTeamOnePower(): int
    {
        return $this->teamOnePower;
    }

    public function getTeamTwoPower(): int
    {
        return $this->teamTwoPower;
    }
}

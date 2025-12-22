<?php

namespace App\Infrastructure\Response;

use DateTimeInterface;

final class RobotDanceOffScoreboardResponse
{
    public function __construct(
        private int $battleId,
        private int $totalMatches,
        private int $lastBattleReplayId,
        private DateTimeInterface $lastPlayedAt,
        private ?string $winningTeamName,
        private string $teamOneName,
        private string $teamTwoName
    ) {
    }

    public function getBattleId(): int
    {
        return $this->battleId;
    }

    public function getTotalMatches(): int
    {
        return $this->totalMatches;
    }

    public function getLastBattleReplayId(): int
    {
        return $this->lastBattleReplayId;
    }

    public function getLastPlayedAt(): string
    {
        return $this->lastPlayedAt->format('Y-m-d H:i:s');
    }

    public function getWinningTeamName(): ?string
    {
        return $this->winningTeamName;
    }

    public function getTeamOneName(): string
    {
        return $this->teamOneName;
    }

    public function getTeamTwoName(): string
    {
        return $this->teamTwoName;
    }

    public function toArray(): array
    {
        return [
            'battleId' => $this->getBattleId(),
            'totalMatches' => $this->getTotalMatches(),
            'lastBattleReplayId' => $this->getLastBattleReplayId(),
            'lastPlayedAt' => $this->getLastPlayedAt(),
            'winningTeamName' => $this->getWinningTeamName(),
            'teamOneName' => $this->getTeamOneName(),
            'teamTwoName' => $this->getTeamTwoName(),
        ];
    }
}

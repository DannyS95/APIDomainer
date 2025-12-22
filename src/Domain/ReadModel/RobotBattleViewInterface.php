<?php

namespace App\Domain\ReadModel;

use DateTimeImmutable;

interface RobotBattleViewInterface
{
    /**
     * Unique identifier of the dance-off occurrence (battle replay id).
     */
    public function getBattleReplayId(): int;

    /**
     * Identifier of the aggregate battle that groups original match and its replays.
     */
    public function getBattleId(): int;

    public function getCreatedAt(): DateTimeImmutable;

    /**
     * @return array{id: int, name: string, codeName: string, robots: array<int, array<string, mixed>>}
     */
    public function getTeamOne(): array;

    public function getTeamOneCodeName(): string;

    public function getTeamOnePower(): int;

    /**
     * @return array{id: int, name: string, codeName: string, robots: array<int, array<string, mixed>>}
     */
    public function getTeamTwo(): array;

    public function getTeamTwoCodeName(): string;

    public function getTeamTwoPower(): int;

    /**
     * @return array{id: int, name: string, codeName: string, robots: array<int, array<string, mixed>>}|null
     */
    public function getWinningTeam(): ?array;

    /**
     * @return list<int>
     */
    public function getTeamOneRobotIds(): array;

    /**
     * @return list<int>
     */
    public function getTeamTwoRobotIds(): array;
}

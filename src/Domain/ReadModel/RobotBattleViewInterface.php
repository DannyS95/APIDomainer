<?php

namespace App\Domain\ReadModel;

use DateTimeImmutable;

interface RobotBattleViewInterface
{
    public function getId(): int;

    public function getCreatedAt(): DateTimeImmutable;

    /**
     * @return array{id: int, name: string, robots: array<int, array<string, mixed>>}
     */
    public function getTeamOne(): array;

    /**
     * @return array{id: int, name: string, robots: array<int, array<string, mixed>>}
     */
    public function getTeamTwo(): array;

    /**
     * @return array{id: int, name: string, robots: array<int, array<string, mixed>>}|null
     */
    public function getWinningTeam(): ?array;
}

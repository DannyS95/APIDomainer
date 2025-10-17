<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotBattle;

interface RobotBattleRepositoryInterface
{
    public function findOneBy(int $id): ?RobotBattle;

    public function save(RobotBattle $battle): void;

    /**
     * @return array<int, RobotBattle>
     */
    public function findAll(): array;
}

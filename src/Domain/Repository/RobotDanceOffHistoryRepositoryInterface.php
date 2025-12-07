<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOffHistory;

interface RobotDanceOffHistoryRepositoryInterface
{
    public function findOneBy(int $id): ?RobotDanceOffHistory;

    public function save(RobotDanceOffHistory $battle): void;

    /**
     * @return array<int, RobotDanceOffHistory>
     */
    public function findAll(): array;
}

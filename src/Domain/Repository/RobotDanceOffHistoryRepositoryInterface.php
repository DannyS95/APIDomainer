<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOffHistory;

interface RobotDanceOffHistoryRepositoryInterface
{
    public function findOneById(int $id): ?RobotDanceOffHistory;

    public function save(RobotDanceOffHistory $battle): void;

    /**
     * @return array<int, RobotDanceOffHistory>
     */
    public function findAll(): array;

    /**
     * @return array<int, RobotDanceOffHistory>
     */
    public function findByPeriod(int $year, int $quarter, int $page, int $perPage): array;
}

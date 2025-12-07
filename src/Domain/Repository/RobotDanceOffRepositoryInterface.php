<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\RobotDanceOffHistory;
use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\ValueObject\FilterCriteria;

interface RobotDanceOffRepositoryInterface
{
    /**
     * @return array<int, RobotBattleViewInterface>
     */
    public function findAll(FilterCriteria $filterCriteria): array;

    public function findOneBy(int $id): ?RobotDanceOff;

    public function save(RobotDanceOff $robotDanceOff): void;

    public function delete(RobotDanceOff $robotDanceOff): void;

    public function findLatestByBattle(RobotDanceOffHistory $battle): ?RobotDanceOff;
}

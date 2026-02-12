<?php

namespace App\Domain\Repository;

use App\Domain\ReadModel\RobotBattleViewInterface;
use App\Domain\ValueObject\FilterCriteria;

interface RobotBattleViewReadRepositoryInterface
{
    /**
     * @return list<RobotBattleViewInterface>
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array;
}

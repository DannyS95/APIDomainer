<?php

namespace App\Domain\Repository;

use App\Domain\ValueObject\FilterCriteria;

interface RobotReadRepositoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function findByCriteria(FilterCriteria $filterCriteria): array;
}

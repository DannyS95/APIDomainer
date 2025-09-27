<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Robot;
use App\Domain\ValueObject\FilterCriteria;

interface RobotRepositoryInterface
{
    public function findAll(FilterCriteria $filterCriteria): array;

    public function findOneBy(int $id): ?Robot;
}

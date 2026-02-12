<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Robot;

interface RobotRepositoryInterface
{
    public function findOneById(int $id): ?Robot;
}

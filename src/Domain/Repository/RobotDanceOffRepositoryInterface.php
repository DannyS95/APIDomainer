<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Domain\Entity\RobotDanceOffHistory;

interface RobotDanceOffRepositoryInterface
{
    public function findOneById(int $id): ?RobotDanceOff;

    public function save(RobotDanceOff $robotDanceOff): void;

    public function delete(RobotDanceOff $robotDanceOff): void;

    public function findLatestByBattle(RobotDanceOffHistory $battle): ?RobotDanceOff;
}

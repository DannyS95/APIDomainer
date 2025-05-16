<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Application\DTO\ApiFiltersDTO;

interface RobotDanceOffRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array;

    public function findOneBy(int $id): ?RobotDanceOff;

    public function save(RobotDanceOff $robotDanceOff): void;

    public function delete(RobotDanceOff $robotDanceOff): void;
}

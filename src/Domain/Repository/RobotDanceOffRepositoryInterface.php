<?php

namespace App\Domain\Repository;

use App\Domain\Entity\RobotDanceOff;
use App\Infrastructure\DTO\ApiFiltersDTO;

interface RobotDanceOffRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array;

    public function bulkSave(array $robotDanceOff): void;
}
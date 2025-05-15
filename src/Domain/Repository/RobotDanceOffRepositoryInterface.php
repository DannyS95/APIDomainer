<?php

namespace App\Domain\Repository;

use App\Application\DTO\ApiFiltersDTO;

interface RobotDanceOffRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array;

    public function bulkSave(array $robotDanceOff): void;
}
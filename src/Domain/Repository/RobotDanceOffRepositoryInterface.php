<?php

namespace App\Domain\Repository;

use App\Infrastructure\DTO\ApiFiltersDTO;

interface RobotDanceOffRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array;
}

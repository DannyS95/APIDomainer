<?php

namespace App\Domain\Repository;

use App\Infrastructure\DTO\ApiFiltersDTO;

interface RobotRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO);
}

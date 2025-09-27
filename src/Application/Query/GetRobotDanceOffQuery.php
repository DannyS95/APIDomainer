<?php

namespace App\Application\Query;

use App\Application\DTO\ApiFiltersDTO;

final class GetRobotDanceOffQuery
{
    public function __construct(private readonly ApiFiltersDTO $apiFiltersDTO)
    {
    }

    public function getApiFiltersDTO(): ApiFiltersDTO
    {
        return $this->apiFiltersDTO;
    }
}

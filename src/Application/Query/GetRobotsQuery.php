<?php

namespace App\Application\Query;

use App\Application\DTO\ApiFiltersDTO;
use Symfony\Component\Validator\Constraints as Assert;

final class GetRobotsQuery
{
    public function __construct(
        #[Assert\NotNull]
        private readonly ApiFiltersDTO $apiFiltersDTO
    )
    {
    }

    public function getApiFiltersDTO(): ApiFiltersDTO
    {
        return $this->apiFiltersDTO;
    }
}

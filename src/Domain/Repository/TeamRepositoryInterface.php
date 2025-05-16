<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Team;
use App\Application\DTO\ApiFiltersDTO;

interface TeamRepositoryInterface
{
    public function findAll(ApiFiltersDTO $apiFiltersDTO): array;

    public function findOneBy(int $id): ?Team;

    public function save(Team $team): void;

    public function delete(Team $team): void;
}
